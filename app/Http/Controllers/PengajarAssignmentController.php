<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\PaketPelatihanUnit;
use App\Models\PaketPelatihanPengajarProgram;
use App\Models\PaketPelatihanPengajarSubUnit;
use App\Models\Instructor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajarAssignmentController extends Controller
{
    /**
     * Assign pengajar ke program (dari halaman program)
     */
    public function assignToProgram(Request $request, Program $program)
    {
        $validated = $request->validate([
            'jenis_materi_pelatihan_id' => 'required|exists:jenis_materi_pelatihan,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'required_if:pengajar_tipe,internal|nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'required_if:pengajar_tipe,eksternal|nullable|exists:pengajar_eksternal,id',
        ]);

        // Cek duplikasi assignment
        $exists = PaketPelatihanPengajarProgram::where('programs_id', $program->id)
            ->where('jenis_materi_pelatihan_id', $validated['jenis_materi_pelatihan_id'])
            ->where(function ($q) use ($validated) {
                if ($validated['pengajar_tipe'] === 'eksternal') {
                    $q->where('pengajar_eksternal_id', $validated['pengajar_eksternal_id'])
                      ->where('pengajar_eksternal', 'Y');
                } else {
                    $q->where('pengajar_internal_id', $validated['pengajar_internal_id'])
                      ->where('pengajar_eksternal', 'N');
                }
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Pengajar ini sudah di-assign ke program dan jenis materi yang sama!');
        }

        try {
            DB::beginTransaction();

            PaketPelatihanPengajarProgram::create([
                'jenis_materi_pelatihan_id' => $validated['jenis_materi_pelatihan_id'],
                'pengajar_eksternal' => $validated['pengajar_tipe'] === 'eksternal' ? 'Y' : 'N',
                'pengajar_internal_id' => $validated['pengajar_internal_id'] ?? null,
                'pengajar_eksternal_id' => $validated['pengajar_eksternal_id'] ?? null,
                'programs_id' => $program->id,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Pengajar berhasil ditugaskan ke program!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menugaskan pengajar: ' . $e->getMessage());
        }
    }

    /**
     * Update assignment program
     */
    public function updateProgram(Request $request, PaketPelatihanPengajarProgram $assignment)
    {
        $validated = $request->validate([
            'jenis_materi_pelatihan_id' => 'required|exists:jenis_materi_pelatihan,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'required_if:pengajar_tipe,internal|nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'required_if:pengajar_tipe,eksternal|nullable|exists:pengajar_eksternal,id',
            'programs_id' => 'required|exists:programs,id',
        ]);

        try {
            DB::beginTransaction();

            $assignment->update([
                'jenis_materi_pelatihan_id' => $validated['jenis_materi_pelatihan_id'],
                'pengajar_eksternal' => $validated['pengajar_tipe'] === 'eksternal' ? 'Y' : 'N',
                'pengajar_internal_id' => $validated['pengajar_internal_id'] ?? null,
                'pengajar_eksternal_id' => $validated['pengajar_eksternal_id'] ?? null,
                'programs_id' => $validated['programs_id'],
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Assignment berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui assignment: ' . $e->getMessage());
        }
    }

    /**
     * Assign pengajar ke sub unit
     */
    /**
     * Assign pengajar ke sub unit
     */
    public function assignToSubUnit(Request $request)
    {
        $validated = $request->validate([
            'pp_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'programs_id' => 'required|exists:programs,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'nullable|exists:pengajar_eksternal,id',
        ]);

        $exists = PaketPelatihanPengajarSubUnit::where('pp_unit_id', $validated['pp_unit_id'])
            ->where('programs_id', $validated['programs_id'])
            ->where(function ($q) use ($validated) {
                if ($validated['pengajar_tipe'] === 'eksternal') {
                    $q->where('pengajar_eksternal_id', $validated['pengajar_eksternal_id'])
                      ->where('pengajar_eksternal', 'Y');
                } else {
                    $q->where('pengajar_internal_id', $validated['pengajar_internal_id'])
                      ->where('pengajar_eksternal', 'N');
                }
            })
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Pengajar ini sudah di-assign ke sub unit dan program yang sama!');
        }

        try {
            DB::beginTransaction();

            $data = [
                'pp_unit_id' => $validated['pp_unit_id'],
                'programs_id' => $validated['programs_id'],
                'user_id' => auth()->id(),
            ];

            if ($validated['pengajar_tipe'] === 'eksternal') {
                $data['pengajar_eksternal'] = 'Y';
                $data['pengajar_eksternal_id'] = $validated['pengajar_eksternal_id'] ?? null;
                $data['pengajar_internal_id'] = null;
            } else {
                $data['pengajar_eksternal'] = 'N';
                $data['pengajar_internal_id'] = $validated['pengajar_internal_id'] ?? null;
                $data['pengajar_eksternal_id'] = null;
            }

            PaketPelatihanPengajarSubUnit::create($data);

            DB::commit();
            return redirect()->back()->with('success', 'Pengajar berhasil ditugaskan ke sub unit!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menugaskan pengajar: ' . $e->getMessage());
        }
    }

    /**
     * Update assignment sub unit
     */
    /**
     * Update assignment sub unit
     */
    public function updateSubUnit(Request $request, PaketPelatihanPengajarSubUnit $assignment)
    {
        $validated = $request->validate([
            'pp_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'programs_id' => 'required|exists:programs,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'required_if:pengajar_tipe,internal|nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'required_if:pengajar_tipe,eksternal|nullable|exists:pengajar_eksternal,id',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'pp_unit_id' => $validated['pp_unit_id'],
                'programs_id' => $validated['programs_id'],
            ];

            if ($validated['pengajar_tipe'] === 'eksternal') {
                $data['pengajar_eksternal'] = 'Y';
                $data['pengajar_eksternal_id'] = $validated['pengajar_eksternal_id'] ?? null;
                $data['pengajar_internal_id'] = null;
            } else {
                $data['pengajar_eksternal'] = 'N';
                $data['pengajar_internal_id'] = $validated['pengajar_internal_id'] ?? null;
                $data['pengajar_eksternal_id'] = null;
            }

            $assignment->update($data);

            DB::commit();
            return redirect()->back()->with('success', 'Assignment sub unit berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui assignment: ' . $e->getMessage());
        }
    }

    /**
     * Remove pengajar dari program
     */
    public function removeFromProgram(PaketPelatihanPengajarProgram $assignment)
    {
        $assignment->delete();
        return redirect()->back()->with('success', 'Pengajar berhasil dihapus dari program!');
    }

    /**
     * Remove pengajar dari sub unit
     */
    public function removeFromSubUnit(PaketPelatihanPengajarSubUnit $assignment)
    {
        if (!$assignment->exists) {
            return redirect()->back()->with('error', 'Assignment tidak ditemukan!');
        }

        try {
            $assignment->delete(); // Ini akan soft delete (isi deleted_at)

            return redirect()->back()->with('success', 'Pengajar berhasil dihapus dari sub unit!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus assignment: ' . $e->getMessage());
        }
    }
}
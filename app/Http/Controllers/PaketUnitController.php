<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\PaketPelatihan;
use App\Models\PaketPelatihanUnit;
use App\Models\ProgramPelatihanUnits;
use App\Models\MasterProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaketUnitController extends Controller
{
    /**
     * Store paket pelatihan unit
     * URL: POST /admin/programs/paket-pelatihan/{paket}/paket-units
     */
    public function store(Request $request, $paketId)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'program_pelatihan_unit_id' => 'required|exists:program_pelatihan_units,id',
                'master_program_sub_unit_id' => 'required|exists:master_programs,id',
                'jp' => 'nullable|integer|min:0',
                'sub_unit_kompetensi' => 'required|in:Y,N',
            ]);

            DB::beginTransaction();

            // ✅ PERBAIKAN: Ambil paket pelatihan
            $paket = PaketPelatihan::findOrFail($paketId);
            
            // ✅ PERBAIKAN: Ambil atau buat Program untuk paket ini
            $program = $paket->programs()->first();
            
            if (!$program) {
                // Jika belum ada program, buat program baru untuk paket ini
                // Atau return error jika harus ada program terlebih dahulu
                DB::rollBack();
                return redirect()->back()->with('error', 'Paket pelatihan ini belum memiliki program. Silakan tambahkan program terlebih dahulu.');
            }

            // Cek duplikasi
            $exists = PaketPelatihanUnit::where('programs_id', $program->id)
                ->where('program_pelatihan_unit_id', $validated['program_pelatihan_unit_id'])
                ->where('master_program_sub_unit_id', $validated['master_program_sub_unit_id'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Unit kompetensi ini sudah ditambahkan ke paket.');
            }

            // ✅ PERBAIKAN: Tambahkan programs_id dari program yang ditemukan
            $validated['programs_id'] = $program->id;

            // Create paket pelatihan unit
            PaketPelatihanUnit::create($validated);

            DB::commit();

            return redirect()->back()->with('success', 'Unit berhasil ditambahkan ke paket.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali input Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paket pelatihan unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete paket pelatihan unit
     * URL: DELETE /admin/programs/paket-pelatihan/{paket}/paket-units/{paketUnit}
     */
    public function destroy($paketId, $paketUnitId)
    {
        try {
            DB::beginTransaction();

            $paket = PaketPelatihan::findOrFail($paketId);
            $program = $paket->programs()->first();
            
            if (!$program) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Program tidak ditemukan.');
            }

            $paketUnit = PaketPelatihanUnit::where('programs_id', $program->id)
                ->where('id', $paketUnitId)
                ->firstOrFail();

            // Delete related sub units first
            $paketUnit->paketPelatihanSubUnits()->delete();

            $paketUnit->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Unit berhasil dihapus dari paket.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting paket pelatihan unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get units for a paket (for AJAX/display purposes)
     */
    public function getUnits($paketId)
    {
        try {
            $paket = PaketPelatihan::findOrFail($paketId);
            $program = $paket->programs()->first();
            
            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program tidak ditemukan untuk paket ini',
                    'data' => []
                ]);
            }

            $units = PaketPelatihanUnit::with([
                'programPelatihanUnit.independentCompetencyUnit',
                'masterProgramSubUnit',
                'program'
            ])
            ->where('programs_id', $program->id)
            ->get();

            return response()->json([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
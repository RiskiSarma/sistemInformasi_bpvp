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
            $validated = $request->validate([
                'program_id'                 => 'required|exists:programs,id',  // ← tambah validasi program_id
                'program_pelatihan_unit_id'  => 'required|exists:program_pelatihan_units,id',
                'master_program_sub_unit_id' => 'required|exists:master_programs,id',
                'jp'                         => 'nullable|integer|min:0',
                'sub_unit_kompetensi'        => 'required|in:Y,N',
            ]);

            DB::beginTransaction();

            $paket = PaketPelatihan::findOrFail($paketId);

            // ← Gunakan program_id dari form, bukan first()
            $program = $paket->programs()->where('id', $validated['program_id'])->firstOrFail();

            // Cek duplikasi
            $exists = PaketPelatihanUnit::where('programs_id', $program->id)
                ->where('program_pelatihan_unit_id', $validated['program_pelatihan_unit_id'])
                ->where('master_program_sub_unit_id', $validated['master_program_sub_unit_id'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Unit kompetensi ini sudah ditambahkan ke paket.');
            }

            PaketPelatihanUnit::create([
                'programs_id'                => $program->id,
                'program_pelatihan_unit_id'  => $validated['program_pelatihan_unit_id'],
                'master_program_sub_unit_id' => $validated['master_program_sub_unit_id'],
                'jp'                         => $validated['jp'],
                'sub_unit_kompetensi'        => $validated['sub_unit_kompetensi'],
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Unit berhasil ditambahkan ke paket.');

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
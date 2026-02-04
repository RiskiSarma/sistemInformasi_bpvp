<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihan;
use App\Models\PaketPelatihanSubUnit;
use App\Models\PaketPelatihanUnit;
use App\Models\Program;
use App\Models\IndependentCompetencyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaketSubUnitController extends Controller
{
    /**
     * Store paket pelatihan sub unit
     * URL: POST /admin/programs/paket-pelatihan/{paket}/paket-sub-units
     */
    public function store(Request $request, $paketId)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Ambil paket pelatihan
            $paket = PaketPelatihan::findOrFail($paketId);
            $program = $paket->programs()->first();

            if (!$program) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Paket pelatihan ini belum memiliki program.');
            }

            // ✅ Validasi bahwa paket_pelatihan_unit_id yang dipilih benar-benar milik program ini
            $paketUnit = PaketPelatihanUnit::where('id', $validated['paket_pelatihan_unit_id'])
                ->where('programs_id', $program->id)
                ->first();

            if (!$paketUnit) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Paket unit yang dipilih tidak valid untuk program ini.');
            }

            // Cek duplikasi
            $exists = PaketPelatihanSubUnit::where('paket_pelatihan_unit_id', $validated['paket_pelatihan_unit_id'])
                ->where('independent_competency_unit_id', $validated['independent_competency_unit_id'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sub unit kompetensi ini sudah ditambahkan.');
            }

            // ✅ Tambahkan programs_id
            $validated['programs_id'] = $program->id;

            // Create paket pelatihan sub unit
            PaketPelatihanSubUnit::create($validated);

            DB::commit();

            return redirect()->back()->with('success', 'Sub unit kompetensi berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paket pelatihan sub unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete paket pelatihan sub unit
     * URL: DELETE /admin/programs/paket-pelatihan/{paket}/paket-sub-units/{subUnit}
     */
    public function destroy($paketId, $subUnitId)
    {
        try {
            DB::beginTransaction();

            $paket = PaketPelatihan::findOrFail($paketId);
            $program = $paket->programs()->first();

            if (!$program) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Program tidak ditemukan.');
            }

            $subUnit = PaketPelatihanSubUnit::whereHas('paketPelatihanUnit', function($q) use ($program) {
                $q->where('programs_id', $program->id);
            })
            ->where('id', $subUnitId)
            ->firstOrFail();

            $subUnit->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Sub unit kompetensi berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting paket pelatihan sub unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get sub units for paket (AJAX)
     * URL: GET /admin/programs/paket-pelatihan/{paket}/paket-sub-units
     */
    public function getSubUnits($paketId)
    {
        try {
            $paket = PaketPelatihan::findOrFail($paketId);
            $program = $paket->programs()->first();

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'Program tidak ditemukan',
                    'data' => []
                ]);
            }

            $subUnits = PaketPelatihanSubUnit::with([
                'paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit',
                'program',
                'masterProgram',
                'unitKompetensi'
            ])
            ->whereHas('paketPelatihanUnit', function($q) use ($program) {
                $q->where('programs_id', $program->id);
            })
            ->get();

            return response()->json([
                'success' => true,
                'data' => $subUnits
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
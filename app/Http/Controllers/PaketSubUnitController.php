<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihan;
use App\Models\PaketPelatihanSubUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaketSubUnitController extends Controller
{
    /**
     * ✅ GET - Ambil data sub units untuk paket tertentu (AJAX)
     */
    public function getSubUnits($paketId)
    {
        $paket = PaketPelatihan::findOrFail($paketId);
        $currentProgram = $paket->programs->first();
        
        if (!$currentProgram) {
            return response()->json([
                'success' => false,
                'message' => 'Paket belum memiliki program'
            ]);
        }

        $subUnits = PaketPelatihanSubUnit::whereHas('paketPelatihanUnit', function($q) use ($currentProgram) {
                $q->where('programs_id', $currentProgram->id);
            })
            ->with([
                'paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit', 
                'masterProgram', 
                'unitKompetensi'
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subUnits
        ]);
    }

    /**
     * ✅ POST - Tambah sub unit (NAMA KOLOM DISESUAIKAN)
     */
    public function store(Request $request, $paketId)
    {
        // ✅ NAMA FIELD SESUAI NAMA KOLOM DI DATABASE (tanpa _id)
        $validated = $request->validate([
            'paket_pelatihan_unit_id'    => 'required|exists:paket_pelatihan_units,id',
            'master_programs_id'         => 'required|exists:master_programs,id',
            'independent_competency_units' => 'required|exists:independent_competency_units,id', // ✅ tanpa _id
            'jp'                         => 'nullable|integer|min:0',
        ], [
            'paket_pelatihan_unit_id.required'          => 'Paket unit harus dipilih',
            'master_programs_id.required'               => 'Master program harus dipilih',
            'independent_competency_units.required'     => 'Unit kompetensi harus dipilih',
            'jp.integer'                                => 'JP harus berupa angka',
        ]);

        try {
            DB::beginTransaction();

            // Cek duplikasi
            $exists = PaketPelatihanSubUnit::where('paket_pelatihan_unit_id', $validated['paket_pelatihan_unit_id'])
                ->where('independent_competency_units', $validated['independent_competency_units']) // ✅ tanpa _id
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->with('error', 'Sub unit ini sudah ditambahkan!');
            }

            PaketPelatihanSubUnit::create($validated);

            DB::commit();

            return back()->with('success', 'Sub unit berhasil ditambahkan ke paket!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paket sub unit: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan sub unit: ' . $e->getMessage());
        }
    }

    /**
     * ✅ PUT - Update sub unit
     */
    public function update(Request $request, $paketId, $subUnitId)
    {
        $validated = $request->validate([
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_units' => 'required|exists:independent_competency_units,id',  // ✅ TANPA _ID
            'jp' => 'nullable|integer|min:0',
        ]);

        try {
            $subUnit = PaketPelatihanSubUnit::findOrFail($subUnitId);
            
            // Cek duplikasi
            $exists = PaketPelatihanSubUnit::where('paket_pelatihan_unit_id', $subUnit->paket_pelatihan_unit_id)
                ->where('independent_competency_units', $validated['independent_competency_units'])  // ✅ TANPA _ID
                ->where('id', '!=', $subUnitId)
                ->exists();

            if ($exists) {
                return back()->with('error', 'Sub unit ini sudah ada!');
            }

            $subUnit->update($validated);

            return back()->with('success', 'Sub unit berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating paket sub unit: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui sub unit: ' . $e->getMessage());
        }
    }

    /**
     * ✅ DELETE - Hapus sub unit
     */
    public function destroy($paketId, $subUnitId)
    {
        try {
            $subUnit = PaketPelatihanSubUnit::findOrFail($subUnitId);
            $subUnit->delete();

            return back()->with('success', 'Sub unit berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting paket sub unit: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus sub unit: ' . $e->getMessage());
        }
    }
}
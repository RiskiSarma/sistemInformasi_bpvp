<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihanSubUnit;
use App\Models\Program;
use App\Models\PaketPelatihanUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaketSubUnitController extends Controller
{
    /**
     * Store a newly created paket sub-unit
     * Route: POST /admin/programs/{program}/paket-sub-units
     */
    public function store(Request $request, $programId)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_units' => 'required|exists:independent_competency_units,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);
            
            // Validate that the paket_unit belongs to this program
            $paketUnit = PaketPelatihanUnit::where('id', $validated['paket_pelatihan_unit_id'])
                ->where('programs_id', $program->id)
                ->first();
                
            if (!$paketUnit) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Paket unit tidak valid untuk program ini.');
            }
            
            // Check duplicate
            $exists = PaketPelatihanSubUnit::where('paket_pelatihan_unit_id', $validated['paket_pelatihan_unit_id'])
                ->where('master_programs_id', $validated['master_programs_id'])
                ->where('independent_competency_units', $validated['independent_competency_units'])
                ->exists();
                
            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sub-unit dengan kombinasi ini sudah ada.');
            }
            
            // Set the program ID
            // $validated['programs_id'] = $program->id;
            
            // Create sub-unit
            PaketPelatihanSubUnit::create($validated);
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket sub-unit berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paket sub-unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan sub-unit: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified paket sub-unit
     * Route: PUT /admin/programs/{program}/paket-sub-units/{paketSubUnit}
     */
    public function update(Request $request, $programId, $paketSubUnitId)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);
            
            // Validate paket_unit belongs to this program
            $paketUnit = PaketPelatihanUnit::where('id', $validated['paket_pelatihan_unit_id'])
                ->where('programs_id', $program->id)
                ->first();
                
            if (!$paketUnit) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Paket unit tidak valid untuk program ini.');
            }
            
            // Find sub-unit
            $subUnit = PaketPelatihanSubUnit::where('id', $paketSubUnitId)
            ->whereHas('paketPelatihanUnit', function($q) use ($program) {
                $q->where('programs_id', $program->id);
            })
            ->firstOrFail();;
            
            // Check duplicate (exclude current)
            $exists = PaketPelatihanSubUnit::where('paket_pelatihan_unit_id', $validated['paket_pelatihan_unit_id'])
                ->where('master_programs_id', $validated['master_programs_id'])
                ->where('independent_competency_unit_id', $validated['independent_competency_unit_id'])
                ->where('id', '!=', $paketSubUnitId)
                ->exists();
                
            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Sub-unit dengan kombinasi ini sudah ada.');
            }
            
            $subUnit->update($validated);
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket sub-unit berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating paket sub-unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui sub-unit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified paket sub-unit
     * Route: DELETE /admin/programs/{program}/paket-sub-units/{paketSubUnit}
     */
    public function destroy($programId, $paketSubUnitId)
    {
        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);
            
            // Find and delete sub-unit
            $subUnit = PaketPelatihanSubUnit::where('id', $paketSubUnitId)
            ->whereHas('paketPelatihanUnit', function($q) use ($program) {
                $q->where('programs_id', $program->id);
            })
            ->firstOrFail();
        
            $subUnit->delete();
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket sub-unit berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting paket sub-unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus sub-unit: ' . $e->getMessage());
        }
    }
    
    /**
     * Get sub-units for a program (optional - for AJAX if needed)
     */
    public function getSubUnits($programId)
    {
        try {
            $program = Program::findOrFail($programId);
            
            $subUnits = PaketPelatihanSubUnit::whereHas('paketPelatihanUnit', function($q) use ($program) {
                    $q->where('programs_id', $program->id);
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
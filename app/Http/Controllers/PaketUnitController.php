<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihanUnit;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaketUnitController extends Controller
{
    /**
     * Store a newly created paket unit
     * Route: POST /admin/programs/{program}/paket-units
     */
    public function store(Request $request, $programId)
    {
        $validated = $request->validate([
            'program_pelatihan_unit_id' => 'required|exists:program_pelatihan_units,id',
            'master_program_sub_unit_id' => 'required|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
            'sub_unit_kompetensi' => 'required|in:Y,N',
        ]);

        DB::beginTransaction();
        try {
            // Get the program (ini bisa Program atau PaketPelatihanProgram, sesuaikan dengan model Anda)
            $program = Program::findOrFail($programId);
            
            // Check duplicate
            $exists = PaketPelatihanUnit::where('programs_id', $program->id)
                ->where('program_pelatihan_unit_id', $validated['program_pelatihan_unit_id'])
                ->where('master_program_sub_unit_id', $validated['master_program_sub_unit_id'])
                ->exists();
                
            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Unit dengan kombinasi ini sudah ada di program.');
            }
            
            // Set the program ID
            $validated['programs_id'] = $program->id;
            
            // Create the unit
            PaketPelatihanUnit::create($validated);
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket unit berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing paket unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan unit: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified paket unit
     * Route: PUT /admin/programs/{program}/paket-units/{paketUnit}
     */
    public function update(Request $request, $programId, $paketUnitId)
    {
        $validated = $request->validate([
            'program_pelatihan_unit_id' => 'required|exists:program_pelatihan_units,id',
            'master_program_sub_unit_id' => 'required|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
            'sub_unit_kompetensi' => 'required|in:Y,N',
        ]);

        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);
            
            // Find the unit that belongs to this program
            $paketUnit = PaketPelatihanUnit::where('id', $paketUnitId)
                ->where('programs_id', $program->id)
                ->firstOrFail();
            
            // Check duplicate (exclude current record)
            $exists = PaketPelatihanUnit::where('programs_id', $program->id)
                ->where('program_pelatihan_unit_id', $validated['program_pelatihan_unit_id'])
                ->where('master_program_sub_unit_id', $validated['master_program_sub_unit_id'])
                ->where('id', '!=', $paketUnitId)
                ->exists();
                
            if ($exists) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Unit dengan kombinasi ini sudah ada di program.');
            }
            
            $paketUnit->update($validated);
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket unit berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating paket unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui unit: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified paket unit
     * Route: DELETE /admin/programs/{program}/paket-units/{paketUnit}
     */
    public function destroy($programId, $paketUnitId)
    {
        DB::beginTransaction();
        try {
            $program = Program::findOrFail($programId);
            
            // Find the unit
            $paketUnit = PaketPelatihanUnit::where('id', $paketUnitId)
                ->where('programs_id', $program->id)
                ->firstOrFail();
            
            // Delete related sub-units first
            $paketUnit->paketPelatihanSubUnits()->delete();
            
            // Delete the unit
            $paketUnit->delete();
            
            DB::commit();
            return redirect()->route('admin.programs.show', $program->id)
                ->with('success', 'Paket unit dan sub-units terkait berhasil dihapus.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting paket unit: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus unit: ' . $e->getMessage());
        }
    }
    
    /**
     * Get units for a program (optional - for AJAX if needed)
     */
    public function getUnits($programId)
    {
        try {
            $program = Program::findOrFail($programId);
            
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
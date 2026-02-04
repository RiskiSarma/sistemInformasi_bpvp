<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihan;
use App\Models\Program;
use App\Models\PaketPelatihanUnit;
use App\Models\PaketPelatihanSubUnit;
use App\Models\ProgramPelatihanUnits;
use App\Models\MasterProgram;
use App\Models\IndependentCompetencyUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaketPelatihanPivotController extends Controller
{
    /**
     * Store Paket Unit (dari modal)
     */
    public function storePaketUnit(Request $request, $paketPelatihanId)
    {
        $validated = $request->validate([
            'program_pelatihan_unit_id' => 'required|exists:program_pelatihan_units,id',
            'master_program_sub_unit_id' => 'required|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
            'sub_unit_kompetensi' => ['required', Rule::in(['Y', 'N'])],
        ]);

        // Get the first program of this paket pelatihan
        $program = Program::where('paket_pelatihan_id', $paketPelatihanId)->firstOrFail();
        
        $validated['programs_id'] = $program->id;

        PaketPelatihanUnit::create($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket unit berhasil ditambahkan.');
    }

    /**
     * Update Paket Unit
     */
    public function updatePaketUnit(Request $request, $paketPelatihanId, $paketUnitId)
    {
        $validated = $request->validate([
            'program_pelatihan_unit_id' => 'required|exists:program_pelatihan_units,id',
            'master_program_sub_unit_id' => 'required|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
            'sub_unit_kompetensi' => ['required', Rule::in(['Y', 'N'])],
        ]);

        $paketUnit = PaketPelatihanUnit::findOrFail($paketUnitId);
        $paketUnit->update($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket unit berhasil diperbarui.');
    }

    /**
     * Delete Paket Unit
     */
    public function destroyPaketUnit($paketPelatihanId, $paketUnitId)
    {
        $paketUnit = PaketPelatihanUnit::findOrFail($paketUnitId);
        $paketUnit->delete();

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket unit berhasil dihapus.');
    }

    /**
     * Store Paket Sub Unit (dari modal)
     */
    public function storePaketSubUnit(Request $request, $paketPelatihanId)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'jp' => 'nullable|integer|min:0',
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
        ]);

        // Validate that paket_pelatihan_unit_id belongs to this paket pelatihan
        $program = Program::where('paket_pelatihan_id', $paketPelatihanId)->firstOrFail();
        
        $paketUnit = PaketPelatihanUnit::where('id', $validated['paket_pelatihan_unit_id'])
            ->where('programs_id', $program->id)
            ->firstOrFail();

        PaketPelatihanSubUnit::create($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket sub-unit berhasil ditambahkan.');
    }

    /**
     * Update Paket Sub Unit
     */
    public function updatePaketSubUnit(Request $request, $paketPelatihanId, $paketSubUnitId)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'jp' => 'nullable|integer|min:0',
            'master_programs_id' => 'required|exists:master_programs,id',
            'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
        ]);

        $paketSubUnit = PaketPelatihanSubUnit::findOrFail($paketSubUnitId);
        $paketSubUnit->update($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket sub-unit berhasil diperbarui.');
    }

    /**
     * Delete Paket Sub Unit
     */
    public function destroyPaketSubUnit($paketPelatihanId, $paketSubUnitId)
    {
        $paketSubUnit = PaketPelatihanSubUnit::findOrFail($paketSubUnitId);
        $paketSubUnit->delete();

        return redirect()->route('admin.programs.paket-pelatihan.index')
            ->with('success', 'Paket sub-unit berhasil dihapus.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ProgramPelatihanUnit;
use App\Models\MasterProgram;
use Illuminate\Http\Request;

class ProgramUnitController extends Controller
{
    public function index(MasterProgram $program)
    {
        $units = $program->programPelatihanUnits()->paginate(10);
        return view('programs.units.index', compact('program', 'units'));
    }

    public function create(MasterProgram $program)
    {
        return view('programs.units.create', compact('program'));
    }

    public function store(Request $request, MasterProgram $program)
    {
        $validated = $request->validate([
            'type_unit' => 'required|in:skkni,special',
            'unit_kompetensi_id' => 'required|exists:independent_competency_units,id',
            'sub_unit_kompetensi' => 'required|in:Y,N',
            'jp' => 'nullable|integer|min:0',
        ]);

        $program->programPelatihanUnits()->create($validated);

        return redirect()->route('admin.programs.master.show', $program) // atau route kelola pelatihan
            ->with('success', 'Unit kompetensi berhasil ditambahkan ke master program!');
    }

    // Edit, update, destroy mirip, pakai $program->programPelatihanUnits()->findOrFail($id)
}
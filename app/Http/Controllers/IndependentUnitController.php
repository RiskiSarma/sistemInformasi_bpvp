<?php

namespace App\Http\Controllers;

use App\Models\IndependentCompetencyUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndependentUnitController extends Controller
{
    public function index()
    {
        $units = IndependentCompetencyUnit::orderBy('code')->paginate(10);
        return view('programs.independent-units.index', compact('units'));
    }

    public function create()
    {
        return view('programs.independent-units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:independent_competency_units,code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        IndependentCompetencyUnit::create($validated);

        return redirect()->route('admin.independent-units.index')
            ->with('success', 'Unit kompetensi independen berhasil ditambahkan!');
    }

    public function edit(IndependentCompetencyUnit $unit)
    {
        return view('programs.independent-units.edit', compact('unit'));
    }

    public function update(Request $request, IndependentCompetencyUnit $unit)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:independent_competency_units,code,' . $unit->id,
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.independent-units.index')
            ->with('success', 'Unit kompetensi independen berhasil diperbarui!');
    }

    public function show(IndependentCompetencyUnit $unit)
    {
        return view('programs.independent-units.show', compact('unit'));
    }

    public function destroy(IndependentCompetencyUnit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.independent-units.index')
            ->with('success', 'Unit kompetensi independen berhasil dihapus!');
    }
}
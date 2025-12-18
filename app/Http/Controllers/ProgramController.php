<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\MasterProgram;
use App\Models\CompetencyUnit;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    // ========== KELOLA PELATIHAN ==========
    
    public function index(Request $request)
    {
        $query = Program::with(['masterProgram', 'participants']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('masterProgram', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhere('batch', 'like', "%{$search}%");
        }

        $programs = $query->orderby('id','asc')->paginate(10);
        
        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        return view('programs.create', compact('masterPrograms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'batch' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program pelatihan berhasil dibuat!');
    }

    public function edit(Program $program)
    {
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        return view('programs.edit', compact('program', 'masterPrograms'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'batch' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program pelatihan berhasil diperbarui!');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        
        return redirect()->route('admin.programs.index')
            ->with('success', 'Program pelatihan berhasil dihapus!');
    }

    // ========== MASTER PROGRAM ==========
    
    public function master(Request $request)
    {
        $query = MasterProgram::with('competencyUnits');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $masterPrograms = $query->orderby('id', 'asc')->paginate(10);
        
        return view('programs.master', compact('masterPrograms'));
    }

    public function showMaster(MasterProgram $masterProgram)
    {
        $masterProgram->load('competencyUnits', 'programs');
        return view('programs.master-show', compact('masterProgram'));
    }

    public function editMaster(MasterProgram $masterProgram)
    {
        return view('programs.master-edit', compact('masterProgram'));
    }

    public function storeMaster(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:master_programs,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        MasterProgram::create($validated);

        return redirect()->route('admin.programs.master')
            ->with('success', 'Master program berhasil ditambahkan!');
    }

    public function updateMaster(Request $request, MasterProgram $masterProgram)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:master_programs,code,' . $masterProgram->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $masterProgram->update($validated);

        return redirect()->route('admin.programs.master')
            ->with('success', 'Master program berhasil diperbarui!');
    }

    public function destroyMaster(MasterProgram $masterProgram)
    {
        $masterProgram->delete();
        
        return redirect()->route('admin.programs.master')
            ->with('success', 'Master program berhasil dihapus!');
    }

    // ========== UNIT KOMPETENSI ==========
    
    public function units(Request $request)
    {
        $query = CompetencyUnit::with('masterProgram');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('master_program_id') && $request->master_program_id != '') {
            $query->where('master_program_id', $request->master_program_id);
        }

        $units = $query->latest()->paginate(10);
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        
        return view('programs.units', compact('units', 'masterPrograms'));
    }

    public function showUnit(CompetencyUnit $unit)
    {
        $unit->load('masterProgram');
        return view('programs.units-show', compact('unit'));
    }

    public function editUnit(CompetencyUnit $unit)
    {
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        return view('programs.units-edit', compact('unit', 'masterPrograms'));
    }

    public function storeUnit(Request $request)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        CompetencyUnit::create($validated);

        return redirect()->route('admin.programs.units')
            ->with('success', 'Unit kompetensi berhasil ditambahkan!');
    }

    public function updateUnit(Request $request, CompetencyUnit $unit)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.programs.units')
            ->with('success', 'Unit kompetensi berhasil diperbarui!');
    }

    public function destroyUnit(CompetencyUnit $unit)
    {
        $unit->delete();
        
        return redirect()->route('admin.programs.units')
            ->with('success', 'Unit kompetensi berhasil dihapus!');
    }
}
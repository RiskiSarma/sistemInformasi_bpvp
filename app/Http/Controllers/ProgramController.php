<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\MasterProgram;
use App\Models\CompetencyUnit;
use App\Models\IndependentCompetencyUnit;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

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
        $batches = Batch::where('is_active', true)->get();
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        $independentUnits = \App\Models\IndependentCompetencyUnit::orderBy('code')->get();
        return view('programs.create', compact('batches','masterPrograms', 'independentUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'batch_id' => 'required|exists:batches,id',
            'angkatan'          => 'required|string|max:50',
            'independent_competency_unit_id'  => 'nullable|exists:independent_competency_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'independent_competency_unit_ids' => 'required|array|min:1',
            'independent_competency_unit_ids.*' => 'exists:independent_competency_units,id',
        ]);

        $program = Program::create($validated);
        $program->independentCompetencyUnits()->sync($request->independent_competency_unit_ids);

        // Kirim notifikasi
        $admins = User::where('role', 'admin')->get(); 
        Notification::send($admins, new GeneralActivityNotification(
            $program,
            Auth::user(),
            'Pelatihan',
            'ditambahkan'
        ));
        
        return redirect()->route('admin.programs.index')
            ->with('success', 'Program pelatihan berhasil dibuat!');
    }

    public function edit(Program $program)
    {
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        $batches = Batch::where('is_active', true)->get();
        $independentUnits = \App\Models\IndependentCompetencyUnit::orderBy('code')->get();
        return view('programs.edit', compact('program', 'masterPrograms', 'batches','independentUnits'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'batch' => 'required|string|max:50',
            'angkatan'          => 'required|string|max:50',
            'independent_competency_unit_id'  => 'nullable|exists:independent_competency_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'independent_competency_unit_ids' => 'required|array|min:1',
            'independent_competency_unit_ids.*' => 'exists:independent_competency_units,id',
        ]);

        $program->update($validated);
        $program->independentCompetencyUnits()->sync($request->independent_competency_unit_ids);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $program,
            Auth::user(),
            'Pelatihan',
            'diperbarui'
        ));

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program pelatihan berhasil diperbarui!');
    }

    public function show(Program $program)
    {
        $program->load([
            'masterProgram',
            'participants',
            'creator',
            'updater'
        ]);

        return view('programs.show', compact('program'));
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
        // $query = MasterProgram::with('competencyUnits');
        $query = MasterProgram::query();

        // Load relasi baru (independentCompetencyUnits)
        // $query->with('independentCompetencyUnits');

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
        $masterProgram->load(
            // 'competencyUnits', 
            'programs.independentCompetencyUnits',
            'programs', 
            'creator', 
            'updater');
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
            'kejuruan'        => 'required|string|max:100',
            'bidang'          => 'required|string|max:100',
            'jenis_pelatihan' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $masterProgram = MasterProgram::create($validated);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $masterProgram,
            Auth::user(),
            'Master Program',
            'ditambahkan'
        ));

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
            'kejuruan'        => 'required|string|max:100',
            'bidang'          => 'required|string|max:100',
            'jenis_pelatihan' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['updated_by'] = auth()->id();

        $masterProgram->update($validated);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $masterProgram,
            Auth::user(),
            'Master Program',
            'diperbarui'
        ));

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
        $unit->load('masterProgram', 'creator', 'updater');
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

        $unit = CompetencyUnit::create($validated);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            Auth::user(),
            'Unit Kompetensi',
            'ditambahkan'
        ));

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

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            Auth::user(),
            'Unit Kompetensi',
            'diperbarui'
        ));

        return redirect()->route('admin.programs.units')
            ->with('success', 'Unit kompetensi berhasil diperbarui!');
    }

    public function syncKemnaker()
    {
        // $this->info('Memulai sync program dari Kemnaker...');

        Artisan::call('kemnaker:sync-programs', [
            '--limit' => 100,
            '--page' => 1,
        ]);

        $output = Artisan::output();

        return redirect()->route('admin.programs.master')
            ->with('success', 'Sync program dari Kemnaker berhasil! ' . trim($output));
    }

    public function destroyUnit(CompetencyUnit $unit)
    {
        $unit->delete();
        
        return redirect()->route('admin.programs.units')
            ->with('success', 'Unit kompetensi berhasil dihapus!');
    }
    public function storeIndependentUnitToMaster(Request $request, MasterProgram $masterProgram)
{
    $validated = $request->validate([
        'code' => 'required|string|max:50|unique:independent_competency_units,code',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $unit = IndependentCompetencyUnit::create($validated);

    // Attach ke semua programs di bawah master ini
    foreach ($masterProgram->programs as $program) {
        $program->independentCompetencyUnits()->syncWithoutDetaching($unit->id);
    }

    return redirect()->route('admin.programs.master.show', $masterProgram)
        ->with('success', 'Unit kompetensi independen berhasil ditambahkan ke master!');
}

public function updateIndependentUnitInMaster(Request $request, MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
{
    $validated = $request->validate([
        'code' => 'required|string|max:50|unique:independent_competency_units,code,' . $independentCompetencyUnit->id,
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $independentCompetencyUnit->update($validated);

    return redirect()->route('admin.programs.master.show', $masterProgram)
        ->with('success', 'Unit kompetensi independen berhasil diperbarui!');
}

public function destroyIndependentUnitInMaster(MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
{
    // Detach dari semua programs di bawah master
    foreach ($masterProgram->programs as $program) {
        $program->independentCompetencyUnits()->detach($independentCompetencyUnit->id);
    }

    $independentCompetencyUnit->delete();

    return redirect()->route('admin.programs.master.show', $masterProgram)
        ->with('success', 'Unit kompetensi independen berhasil dihapus!');
}
}
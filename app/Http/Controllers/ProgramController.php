<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\MasterProgram;
use App\Models\CompetencyUnit;
use App\Models\IndependentCompetencyUnit;
use App\Models\Batch;
use App\Models\PaketPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GeneralActivityNotification;
use Illuminate\Support\Facades\Artisan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProgramController extends Controller
{
    // ========== KELOLA PELATIHAN ==========
    
    public function index(Request $request)
    {
        $query = Program::with(['masterProgram', 'participants', 'paketPelatihan']);

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
        // $batches = Batch::where('is_active', true)->get();
        $programs = Program::with(['masterProgram', 'paketPelatihan'])->get();
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        $independentUnits = \App\Models\IndependentCompetencyUnit::orderBy('code')->get();
        $paketPelatihans = PaketPelatihan::all();

        return view('programs.create', compact('masterPrograms', 'independentUnits', 'paketPelatihans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            // 'batch_id' => 'required|exists:batches,id',
            'angkatan'          => 'required|string|max:50',
            'independent_competency_unit_id'  => 'nullable|exists:independent_competency_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'independent_competency_unit_ids' => 'required|array|min:1',
            'independent_competency_unit_ids.*' => 'exists:independent_competency_units,id',
            'paket_pelatihan_id' => 'nullable|exists:paket_pelatihans,id', // opsional
            'ada_industri' => 'required|in:Y,N',
            'jp_harian' => 'nullable|integer|min:0',
            'jp' => 'nullable|integer|min:0',
        ],
        [
            'independent_competency_unit_ids.required' => 'Silakan pilih minimal satu unit kompetensi independen.',
            'independent_competency_unit_ids.*.exists' => 'Unit kompetensi independen yang dipilih tidak valid.',
        ]        
        );

        $program = Program::create($validated);
        $program->independentCompetencyUnits()->sync($request->input('independent_competency_unit_ids', []));

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
        $programs = Program::with(['masterProgram', 'paketPelatihan'])->get();
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        // $batches = Batch::where('is_active', true)->get();
        $independentUnits = \App\Models\IndependentCompetencyUnit::orderBy('code')->get();
        $paketPelatihans = PaketPelatihan::all();
        
        return view('programs.edit', compact('program', 'masterPrograms', 'independentUnits', 'paketPelatihans'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            // 'batch_id'          => 'required|exists:batches,id',
            'angkatan'          => 'required|string|max:50',
            // 'independent_competency_unit_id'  => 'nullable|exists:independent_competency_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'independent_competency_unit_ids' => 'required|array|min:1',
            'independent_competency_unit_ids.*' => 'exists:independent_competency_units,id',
            'paket_pelatihan_id' => 'nullable|exists:paket_pelatihans,id',
            'ada_industri' => 'required|in:Y,N',
            'jp_harian' => 'nullable|integer|min:0',
            'jp' => 'nullable|integer|min:0',
        ]);

        $program->update($validated);
        $program->independentCompetencyUnits()->sync($request->input('independent_competency_unit_ids', []));

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
        $masterProgram->load([
            'programs' => function ($query) {
                $query->with([
                    'programUnits' => function ($q) {
                        $q->with('independentCompetencyUnit.skkni');
                    },
                    'participants' => function ($q) {
                        $q->select('id', 'program_id'); // cukup untuk count
                    }
                ])->withCount('participants');
            },
            'creator',
            'updater'
        ]);

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
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'bidang_pelatihan_id' => 'required|exists:bidang_pelatihans,id',
            'versi' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
            'file_program' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // max 5MB
            'is_active' => 'boolean',
        ]);

        $validated['program_pelatihan'] = $validated['name'];
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        // Handle upload file
        if ($request->hasFile('file_program')) {
            $originalName = $request->file('file_program')->getClientOriginalName();
            $path = $request->file('file_program')->storeAs('program-files', $originalName, 'public');
            $validated['file_program'] = $path;
        }

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
            'kejuruan_id' => 'required|exists:kejuruans,id',
            'bidang_pelatihan_id' => 'required|exists:bidang_pelatihans,id',
            'versi' => 'required|integer|min:1',
            'tanggal' => 'nullable|date',
            'file_program' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_active' => 'boolean',
        ]);

        $validated['program_pelatihan'] = $validated['name'];
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['updated_by'] = auth()->id();

        // Handle upload file (ganti jika ada file baru)
        if ($request->hasFile('file_program')) {
            // Hapus file lama jika ada
            if ($masterProgram->file_program) {
                Storage::disk('public')->delete($masterProgram->file_program);
            }
            $originalName = $request->file('file_program')->getClientOriginalName();
            $path = $request->file('file_program')->storeAs('program-files', $originalName, 'public');
            $validated['file_program'] = $path;
        }
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

    public function previewFile(MasterProgram $masterProgram)
    {
        if (!$masterProgram->file_program) {
            abort(404, 'File tidak ditemukan');
        }

        $path = storage_path('app/public/' . $masterProgram->file_program);
        
        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);
        
        // Hanya support PDF untuk preview
        if ($fileExtension !== 'pdf') {
            // Untuk doc/docx, redirect ke download langsung
            return response()->download($path);
        }

        // Untuk PDF, tampilkan inline di browser
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
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
            '--page' => 98,
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
    // ========== PROGRAM PELATIHAN UNITS (PIVOT dengan Independent Units) ==========

    public function storeUnitToMaster(Request $request, MasterProgram $masterProgram)
{
    $validated = $request->validate([
        'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
        'type_unit' => 'required|in:skkni,non-skkni',
        'jp' => 'nullable|integer|min:0',
    ]);

    // Generate UUID manual
    $pivotId = (string) \Illuminate\Support\Str::uuid();

    $masterProgram->independentCompetencyUnits()->attach(
        $validated['independent_competency_unit_id'],
        [
            'id'          => $pivotId,                     // ← tambahkan ini
            'type_unit'   => $validated['type_unit'],
            'jp'          => $validated['jp'] ?? 0,
        ]
    );

    return redirect()->route('admin.programs.master.show', $masterProgram)
        ->with('success', 'Unit kompetensi berhasil ditambahkan ke master program!');
}

    public function destroyUnitInMaster(MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
    {
        // Hapus relasi pivot berdasarkan kedua foreign key
        \DB::table('program_pelatihan_units')
            ->where('master_programs_id', $masterProgram->id)
            ->where('independent_competency_units_id', $independentCompetencyUnit->id)
            ->delete();

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi berhasil dihapus!');
    }
    // Tambahan method untuk store Program di PaketPelatihan
    public function storeForPaket(Request $request, PaketPelatihan $paket)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'batch' => 'required|integer|min:1',
            'angkatan' => 'nullable|string',
            'independent_competency_unit_id' => 'nullable|exists:independent_competency_units,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['required', Rule::in(['planned', 'ongoing', 'completed'])],
            'max_participants' => 'nullable|integer|min:1',
            'ada_industri' => ['nullable', Rule::in(['Y', 'N'])],
            'jp_harian' => 'nullable|integer|min:0',
            'jp' => 'nullable|integer|min:0',
            // Field lain sesuai model Program
        ]);

        $validated['paket_pelatihan_id'] = $paket->id;

        $program = Program::create($validated);

        return redirect()->back()->with('success', 'Program berhasil ditambahkan ke paket pelatihan.');
    }

    // Method destroy untuk Program di Paket
    public function destroyForPaket(PaketPelatihan $paket, Program $program)
    {
        if ($program->paket_pelatihan_id !== $paket->id) {
            abort(403);
        }

        $program->delete();

        return redirect()->back()->with('success', 'Program berhasil dihapus dari paket.');
    }
}
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
use Illuminate\Support\Facades\DB;
use App\Models\Instructor;
use App\Models\ProgramInstructor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProgramController extends Controller
{
    // ========== KELOLA PELATIHAN ==========
    
    public function index(Request $request)
    {
        $query = Program::with([
            'masterProgram', 
            'participants', 
            'paketPelatihan',
            'programInstructors.instructor'
        ]);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('jenis_pelatihan') && $request->jenis_pelatihan != '') {
            $query->whereHas('paketPelatihan', function($q) use ($request) {
                $q->where('jenis_pelatihan', $request->jenis_pelatihan);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('masterProgram', function($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                })
                ->orWhere('angkatan', 'like', "%{$search}%")
                ->orWhere('batch', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderby('id','desc')->paginate(10);
        
        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        // Load master programs dengan units nya
        $masterPrograms = MasterProgram::where('is_active', true)
            ->with(['independentCompetencyUnits' => function($q) {
                $q->orderBy('code');
            }])
            ->get();
        
        $paketPelatihans = PaketPelatihan::orderBy('tahun', 'desc')->get();
        $instructors = Instructor::where('status', 'active')->orderBy('name')->get();

        return view('programs.create', compact(
            'masterPrograms',
            'paketPelatihans',
            'instructors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'paket_pelatihan_id' => 'required|exists:paket_pelatihans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'ada_industri' => 'required|in:Y,N',
            'jp_harian' => 'nullable|integer|min:0',
            'angkatan'  => 'required|in:I,II,III,IV,V,VI,VII,VIII,IX,X',
            
            // Units yang dipilih dari master
            'selected_units' => 'required|array|min:1',
            'selected_units.*' => 'exists:independent_competency_units,id',
            
            // Custom duration per unit
            'unit_durations' => 'required|array',
            'unit_durations.*' => 'integer|min:0',
            
            // Unit types
            'unit_types' => 'required|array',
            'unit_types.*' => 'in:reguler,softskill,industri,skkni',
            
            // Instruktur
            'instructors' => 'required|array|min:1',
            'instructors.*' => 'exists:instructors,id',
            'penanggung_jawab' => 'required|exists:instructors,id',
        ],
        [
            'paket_pelatihan_id.required' => 'Paket Pelatihan harus dipilih.',
            'selected_units.required' => 'Minimal pilih satu unit kompetensi.',
            'instructors.required' => 'Minimal pilih satu instruktur.',
            'penanggung_jawab.required' => 'Harus ada satu instruktur sebagai penanggung jawab.',
        ]);

        DB::beginTransaction();
        try {
            // Build selected units config
            $unitsConfig = [];
            foreach ($request->selected_units as $index => $unitId) {
                $unitsConfig[] = [
                    'unit_id' => (int)$unitId,
                    'custom_duration' => (int)($request->unit_durations[$unitId] ?? 0),
                    'type' => $request->unit_types[$unitId] ?? 'reguler',
                ];
            }

            // Hitung total JP
            $totalJp = collect($unitsConfig)->sum('custom_duration');
            
            $programData = [
            'master_program_id'     => $validated['master_program_id'],
            'paket_pelatihan_id'    => $validated['paket_pelatihan_id'],
            'angkatan'              => $validated['angkatan'],               // ← PASTIKAN ADA
            'start_date'            => $validated['start_date'],
            'end_date'              => $validated['end_date'],
            'status'                => $validated['status'],
            'max_participants'      => $validated['max_participants'] ?? null,
            'ada_industri'          => $validated['ada_industri'],
            'jp_harian'             => $validated['jp_harian'] ?? null,
            'jp'                    => $totalJp,
            'selected_units_config' => $unitsConfig,
            'instructor_id'         => $request->penanggung_jawab,
            'created_by'            => Auth::id(),
            'updated_by'            => Auth::id(),
        ];
            
            
            // Create program
            $program = Program::create($validated);

            // Simpan instruktur
            foreach ($request->instructors as $instructorId) {
                ProgramInstructor::create([
                    'program_id' => $program->id,
                    'instructor_id' => $instructorId,
                    'is_penanggung_jawab' => ($instructorId == $request->penanggung_jawab),
                ]);
            }

            // Kirim notifikasi
            $admins = User::where('role', 'admin')->get(); 
            Notification::send($admins, new GeneralActivityNotification(
                $program,
                Auth::user(),
                'Pelatihan',
                'ditambahkan'
            ));

            DB::commit();
            
            return redirect()->route('admin.programs.index')
                ->with('success', 'Program pelatihan berhasil dibuat dengan Angkatan ' . $program->angkatan . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Program $program)
    {
        $masterPrograms = MasterProgram::where('is_active', true)
            ->with(['independentCompetencyUnits' => function($q) {
                $q->orderBy('code');
            }])
            ->get();
            
        $paketPelatihans = PaketPelatihan::orderBy('tahun', 'desc')->get();
        $instructors = Instructor::where('status', 'active')->orderBy('name')->get();

        // Load relasi
        $program->load(['programInstructors.instructor', 'masterProgram.independentCompetencyUnits']);
        
        return view('programs.edit', compact(
            'program',
            'masterPrograms',
            'paketPelatihans',
            'instructors'
        ));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'master_program_id' => 'required|exists:master_programs,id',
            'paket_pelatihan_id' => 'required|exists:paket_pelatihans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'max_participants' => 'nullable|integer|min:1',
            'ada_industri' => 'required|in:Y,N',
            'jp_harian' => 'nullable|integer|min:0',
            'angkatan' => 'required|string|in:I,II,III,IV,V,VI,VII,VIII,IX,X',
            
            'selected_units' => 'required|array|min:1',
            'selected_units.*' => 'exists:independent_competency_units,id',
            'unit_durations' => 'required|array',
            'unit_durations.*' => 'integer|min:0',
            'unit_types' => 'required|array',
            'unit_types.*' => 'in:reguler,softskill,industri,skkni',
            
            'instructors' => 'required|array|min:1',
            'instructors.*' => 'exists:instructors,id',
            'penanggung_jawab' => 'required|exists:instructors,id',
        ]);

        DB::beginTransaction();
        try {
            // Build config
            $unitsConfig = [];
            foreach ($request->selected_units as $index => $unitId) {
                $unitsConfig[] = [
                    'unit_id' => (int)$unitId,
                    'custom_duration' => (int)($request->unit_durations[$unitId] ?? 0),
                    'type' => $request->unit_types[$unitId] ?? 'reguler',
                ];
            }

            $totalJp = collect($unitsConfig)->sum('custom_duration');
            
            $programData = [
                'master_program_id'     => $validated['master_program_id'],
                'paket_pelatihan_id'    => $validated['paket_pelatihan_id'],
                'angkatan'              => $validated['angkatan'],               // ← PASTIKAN ADA
                'start_date'            => $validated['start_date'],
                'end_date'              => $validated['end_date'],
                'status'                => $validated['status'],
                'max_participants'      => $validated['max_participants'] ?? null,
                'ada_industri'          => $validated['ada_industri'],
                'jp_harian'             => $validated['jp_harian'] ?? null,
                'jp'                    => $totalJp,
                'selected_units_config' => $unitsConfig,
                'instructor_id'         => $request->penanggung_jawab,
                'updated_by'            => Auth::id(),
            ];
            
            
            $program->update($validated);

            // Update instruktur
            $program->programInstructors()->delete();
            foreach ($request->instructors as $instructorId) {
                ProgramInstructor::create([
                    'program_id' => $program->id,
                    'instructor_id' => $instructorId,
                    'is_penanggung_jawab' => ($instructorId == $request->penanggung_jawab),
                ]);
            }

            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new GeneralActivityNotification(
                $program,
                Auth::user(),
                'Pelatihan',
                'diperbarui'
            ));

            DB::commit();

            return redirect()->route('admin.programs.index')
                ->with('success', 'Program pelatihan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Program $program)
    {
        $program->load([
            'masterProgram',
            'paketPelatihan',
            'participants',
            'creator',
            'updater',
            'programInstructors.instructor'
        ]);

        return view('programs.show', compact('program'));
    }

    public function destroy(Program $program)
    {
        DB::beginTransaction();
        try {
            $program->programInstructors()->delete();
            $program->delete();
            
            DB::commit();
            
            return redirect()->route('admin.programs.index')
                ->with('success', 'Program pelatihan berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ========================================
    // ✅ AUTO-GENERATE ANGKATAN
    // ========================================

    /**
     * Hitung angkatan berikutnya secara otomatis
     * GET /admin/programs/next-angkatan
     */
    public function nextAngkatan(Request $request)
    {
        $masterProgramId  = $request->query('master_program_id');
        $paketPelatihanId = $request->query('paket_pelatihan_id');
        $excludeProgramId = $request->query('exclude_program_id'); // untuk edit

        if (!$masterProgramId || !$paketPelatihanId) {
            return response()->json(['angkatan' => 'I', 'info' => 'Angkatan pertama']);
        }

        $angkatanUrutan = ['I','II','III','IV','V','VI','VII','VIII','IX','X',
                           'XI','XII','XIII','XIV','XV','XVI','XVII','XVIII','XIX','XX'];

        $query = Program::where('master_program_id', $masterProgramId)
            ->where('paket_pelatihan_id', $paketPelatihanId);

        if ($excludeProgramId) {
            $query->where('id', '!=', $excludeProgramId);
        }

        $usedAngkatan = $query->pluck('angkatan')->filter()->toArray();

        foreach ($angkatanUrutan as $angkatan) {
            if (!in_array($angkatan, $usedAngkatan)) {
                $info = empty($usedAngkatan)
                    ? 'Angkatan pertama untuk program & paket ini'
                    : 'Sudah ada ' . count($usedAngkatan) . ' angkatan (' . implode(', ', $usedAngkatan) . ')';

                return response()->json([
                    'angkatan' => $angkatan,
                    'info'     => $info,
                ]);
            }
        }

        $next = count($usedAngkatan) + 1;
        return response()->json([
            'angkatan' => 'A-' . $next,
            'info'     => 'Angkatan ke-' . $next,
        ]);
    }

    // ========================================
    // ✅ GENERATE DOKUMEN ADMINISTRASI
    // ========================================

    public function dokumenSkPeserta(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'participants', 'programInstructors.instructor']);
        $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first();

        return view('programs.dokumen.sk-peserta', compact('program', 'pj'));
    }

    public function dokumenStInstruktur(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'programInstructors.instructor']);
        $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first();

        return view('programs.dokumen.st-instruktur', compact('program', 'pj'));
    }

    public function dokumenJadwal(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'programInstructors.instructor']);
        $unitsData = $program->selected_units_with_details;

        return view('programs.dokumen.jadwal', compact('program', 'unitsData'));
    }

    public function dokumenDaftarHadir(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'participants']);

        return view('programs.dokumen.daftar-hadir', compact('program'));
    }

    public function dokumenBiodataPeserta(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'participants']);

        return view('programs.dokumen.biodata-peserta', compact('program'));
    }

    // public function dokumenRekapNilai(Program $program)
    // {
    //     $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'participants']);
    //     $unitsData = $program->selected_units_with_details;

    //     return view('programs.dokumen.rekap-nilai', compact('program', 'unitsData'));
    // }

    public function dokumenSkPenyelenggara(Program $program)
    {
        $program->load([
            'masterProgram.kejuruan',
            'paketPelatihan.jenisPelatihan',
            'participants',
            'programInstructors.instructor',
        ]);

        return view('programs.dokumen.sk-penyelenggara', compact('program'));
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
                    'independentCompetencyUnits' => function ($q) {
                        $q->with('skkni');
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
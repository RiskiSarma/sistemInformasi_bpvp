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
use App\Models\Participant;
use App\Models\ProgramInstructor;
use App\Models\DocumentSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProgramController extends Controller
{
    // ========== KELOLA PELATIHAN ==========

    public function index(Request $request)
    {
        // Sinkronisasi status semua program berdasarkan tanggal
        $this->autoSyncStatuses();

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

        $programs = $query->orderby('id', 'desc')->paginate(10);

        return view('programs.index', compact('programs'));
    }

    public function create()
    {
        $masterPrograms = MasterProgram::where('is_active', true)
            ->with(['independentCompetencyUnits' => function($q) {
                $q->orderBy('code');
            }])
            ->get();

        $paketPelatihans = PaketPelatihan::orderBy('tahun', 'desc')->get();
        $instructors = Instructor::where('status', 'active')->orderBy('name')->get();

        $masterProgramsData = $masterPrograms->map(fn($mp) => [
            'id'    => (string) $mp->id,
            'label' => $mp->code . ' - ' . $mp->name,
            'name'  => $mp->name,
            'units' => $mp->independentCompetencyUnits,
        ]);

        $paketPelatihansData = $paketPelatihans->map(fn($p) => [
            'id'    => (string) $p->id,
            'label' => ($p->jenisPelatihan->jenis_pelatihan ?? 'Unknown') . ' - ' . $p->tahun . ' - Batch ' . $p->batch,
            'jenis' => $p->jenisPelatihan->jenis_pelatihan ?? 'Unknown',
        ]);

        return view('programs.create', compact(
            'masterPrograms', 'paketPelatihans', 'instructors',
            'masterProgramsData', 'paketPelatihansData'
        ));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'master_program_id'  => 'required|exists:master_programs,id',
        'paket_pelatihan_id' => 'required|exists:paket_pelatihans,id',
        'start_date'         => 'required|date',
        'end_date'           => 'required|date|after:start_date',
        'max_participants'   => 'nullable|integer|min:1',
        'ada_industri'       => 'required|in:Y,N',
        'jp_harian'          => 'nullable|integer|min:0',
        'angkatan'           => 'required|in:I,II,III,IV,V,VI,VII,VIII,IX,X',

        'selected_units'     => 'required|array|min:1',
        'selected_units.*'   => 'exists:independent_competency_units,id',

        'unit_durations'     => 'required|array',
        'unit_durations.*'   => 'integer|min:0',

        'unit_types'         => 'required|array',
        'unit_types.*'       => 'in:reguler,softskill,industri,skkni',

        'instructors'        => 'required|array|min:1',
        'instructors.*'      => [
            'required',
            function ($attribute, $value, $fail) {
                $existsInternal = \App\Models\Instructor::where('id', $value)->exists();
                $existsExternal = \App\Models\PengajarEksternal::where('id', $value)->exists();
                if (!$existsInternal && !$existsExternal) {
                    $fail('Instruktur yang dipilih tidak ditemukan.');
                }
            }
        ],
        'penanggung_jawab'   => [
            'required',
            function ($attribute, $value, $fail) {
                $existsInternal = \App\Models\Instructor::where('id', $value)->exists();
                $existsExternal = \App\Models\PengajarEksternal::where('id', $value)->exists();
                if (!$existsInternal && !$existsExternal) {
                    $fail('Penanggung Jawab yang dipilih tidak valid.');
                }
            }
        ],
    ]);

    $validated['status'] = $this->calculateStatus($validated['start_date'], $validated['end_date']);

    DB::beginTransaction();
    try {
        $unitsConfig = [];
        foreach ($request->selected_units as $unitId) {
            $unitsConfig[] = [
                'unit_id'         => (int)$unitId,
                'custom_duration' => (int)($request->unit_durations[$unitId] ?? 0),
                'type'            => $request->unit_types[$unitId] ?? 'reguler',
            ];
        }

        $totalJp = collect($unitsConfig)->sum('custom_duration');

        $isInternalPJ = \App\Models\Instructor::where('id', $request->penanggung_jawab)->exists();

        $program = Program::create(array_merge($validated, [
            'selected_units_config' => $unitsConfig,
            'jp'                    => $totalJp,
            'instructor_id'         => $isInternalPJ ? $request->penanggung_jawab : null,
            'created_by'            => Auth::id(),
            'updated_by'            => Auth::id(),
        ]));

        // Simpan Instruktur (Internal + Eksternal)
        foreach ($request->instructors as $instructorId) {
            $isInternal = \App\Models\Instructor::where('id', $instructorId)->exists();

            if ($isInternal) {
                ProgramInstructor::create([
                    'program_id'          => $program->id,
                    'instructor_id'       => $instructorId,
                    'pengajar_eksternal_id' => null,
                    'instructor_type'     => 'internal',
                    'is_penanggung_jawab' => ($instructorId == $request->penanggung_jawab),
                ]);
            } else {
                ProgramInstructor::create([
                    'program_id'          => $program->id,
                    'instructor_id'       => null,
                    'pengajar_eksternal_id' => $instructorId,
                    'instructor_type'     => 'external',
                    'is_penanggung_jawab' => ($instructorId == $request->penanggung_jawab),
                ]);
            }
        }

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $program, Auth::user(), 'Pelatihan', 'ditambahkan'
        ));

        DB::commit();

        return redirect()->route('admin.programs.index')
            ->with('success', "Program pelatihan berhasil dibuat!");

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function edit(Program $program)
{
    $this->autoSyncStatuses($program->id);
    $program->refresh();

    $masterPrograms = MasterProgram::where('is_active', true)
        ->with(['independentCompetencyUnits' => function($q) {
            $q->orderBy('code');
        }])
        ->get();

    $paketPelatihans = PaketPelatihan::orderBy('tahun', 'desc')->get();

    $internalInstructors = Instructor::where('status', 'active')->orderBy('name')->get();
    $externalInstructors = \App\Models\PengajarEksternal::orderBy('nama')->get();

    $instructors = $internalInstructors->map(function ($item) {
        $item->type = 'internal';
        return $item;
    })->merge(
        $externalInstructors->map(function ($item) {
            $item->type = 'external';
            return $item;
        })
    )->sortBy(fn($i) => $i->name ?? $i->nama);

    $program->load(['programInstructors']);

    // Data untuk Alpine.js
    $initInstructors = $program->programInstructors->map(function($pi) {
        return $pi->instructor_id ?? $pi->pengajar_eksternal_id;
    })->filter()->values()->toArray();

    $initPJ = $program->programInstructors->where('is_penanggung_jawab', true)->first();
    $initPJ = $initPJ ? ($initPJ->instructor_id ?? $initPJ->pengajar_eksternal_id) : '';

    $masterProgramsData = $masterPrograms->map(fn($mp) => [
        'id'    => (string) $mp->id,
        'label' => $mp->code . ' - ' . $mp->name,
        'name'  => $mp->name,
        'units' => $mp->independentCompetencyUnits,
    ]);

    $paketPelatihansData = $paketPelatihans->map(fn($p) => [
        'id'    => (string) $p->id,
        'label' => ($p->jenisPelatihan->jenis_pelatihan ?? 'Unknown') . ' - ' . $p->tahun . ' - Batch ' . $p->batch,
        'jenis' => $p->jenisPelatihan->jenis_pelatihan ?? 'Unknown',
    ]);

    return view('programs.edit', compact(
        'program', 'masterPrograms', 'paketPelatihans', 'instructors',
        'masterProgramsData', 'paketPelatihansData', 'initInstructors', 'initPJ'
    ));
}

    public function removeParticipant(Program $program, Participant $participant)
    {
        $participant->update([
            'program_id' => null,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Peserta berhasil dilepas dari program ini. Data peserta tetap tersimpan di menu Peserta.');
    }

    public function update(Request $request, Program $program)
{
    $validated = $request->validate([
        'master_program_id'  => 'required|exists:master_programs,id',
        'paket_pelatihan_id' => 'required|exists:paket_pelatihans,id',
        'start_date'         => 'required|date',
        'end_date'           => 'required|date|after:start_date',
        'max_participants'   => 'nullable|integer|min:1',
        'ada_industri'       => 'required|in:Y,N',
        'jp_harian'          => 'nullable|integer|min:0',
        'angkatan'           => 'required|string|in:I,II,III,IV,V,VI,VII,VIII,IX,X',

        'selected_units'     => 'required|array|min:1',
        'selected_units.*'   => 'exists:independent_competency_units,id',

        'unit_durations'     => 'required|array',
        'unit_durations.*'   => 'integer|min:0',

        'unit_types'         => 'required|array',
        'unit_types.*'       => 'in:reguler,softskill,industri,skkni',

        'instructors'        => 'required|array|min:1',
        'instructors.*'      => 'required',
        'penanggung_jawab'   => 'required',
    ]);

    $validated['status'] = $this->calculateStatus($validated['start_date'], $validated['end_date']);

    DB::beginTransaction();
    try {
        $unitsConfig = [];
        foreach ($request->selected_units as $unitId) {
            $unitsConfig[] = [
                'unit_id'         => (int)$unitId,
                'custom_duration' => (int)($request->unit_durations[$unitId] ?? 0),
                'type'            => $request->unit_types[$unitId] ?? 'reguler',
            ];
        }

        $totalJp = collect($unitsConfig)->sum('custom_duration');

        $isInternalPJ = \App\Models\Instructor::where('id', $request->penanggung_jawab)->exists();

        $program->update(array_merge($validated, [
            'selected_units_config' => $unitsConfig,
            'jp'                    => $totalJp,
            'instructor_id'         => $isInternalPJ ? $request->penanggung_jawab : null,
            'updated_by'            => Auth::id(),
        ]));

        // ✅ PERBAIKAN: Hapus dan simpan ulang dengan logika yang benar
        $program->programInstructors()->delete();

        foreach ($request->instructors as $instructorId) {
            // Cek apakah internal atau external
            $isInternal = \App\Models\Instructor::where('id', $instructorId)->exists();

            if ($isInternal) {
                // Internal instructor
                ProgramInstructor::create([
                    'program_id'            => $program->id,
                    'instructor_id'         => $instructorId,
                    'pengajar_eksternal_id' => null,
                    'instructor_type'       => 'internal',
                    'is_penanggung_jawab'   => ($instructorId == $request->penanggung_jawab),
                ]);
            } else {
                // External instructor
                ProgramInstructor::create([
                    'program_id'            => $program->id,
                    'instructor_id'         => null,
                    'pengajar_eksternal_id' => $instructorId,
                    'instructor_type'       => 'external',
                    'is_penanggung_jawab'   => ($instructorId == $request->penanggung_jawab),
                ]);
            }
        }

        DB::commit();

        return redirect()->route('admin.programs.index')
            ->with('success', "Program pelatihan berhasil diperbarui!");

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating program: ' . $e->getMessage());
        return redirect()->back()
            ->withInput()
            ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function show($id)
    {
        // Sinkronisasi status program ini berdasarkan tanggal
        $this->autoSyncStatuses((int) $id);

        $program = Program::with([
            'masterProgram',
            'paketPelatihan.jenisPelatihan',
            'participants',
            'creator',
            'updater',
            'programInstructors.instructor',
            'paketPelatihanUnits.programPelatihanUnit.independentCompetencyUnit',
            'programInstructors',
            'programInstructors.instructor',
            'programInstructors.pengajarEksternal',
            'paketPelatihanUnits.masterProgramSubUnit',
            'paketPelatihanUnits.program.masterProgram',
            'paketPelatihanUnits.paketPelatihanSubUnits.masterProgram',
            'paketPelatihanUnits.paketPelatihanSubUnits.unitKompetensi',
            'paketPelatihanUnits.paketPelatihanSubUnits.paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit'
        ])->findOrFail($id);

        $programPelatihanUnits = \App\Models\ProgramPelatihanUnit::with('independentCompetencyUnit')->get();
        $masterPrograms        = \App\Models\MasterProgram::orderBy('name')->get();
        $allCompetencyUnits    = \App\Models\IndependentCompetencyUnit::orderBy('name')->get();

        return view('programs.show', compact('program', 'programPelatihanUnits', 'masterPrograms', 'allCompetencyUnits'));
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

    public function nextAngkatan(Request $request)
    {
        $masterProgramId  = $request->query('master_program_id');
        $paketPelatihanId = $request->query('paket_pelatihan_id');
        $excludeProgramId = $request->query('exclude_program_id');

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
        $pj       = $program->programInstructors->where('is_penanggung_jawab', true)->first();
        $settings = DocumentSetting::where('key', 'sk-peserta')->first();
        return view('programs.dokumen.sk-peserta', compact('program', 'pj', 'settings'));
    }

    public function dokumenStInstruktur(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'programInstructors.instructor']);
        $pj       = $program->programInstructors->where('is_penanggung_jawab', true)->first();
        $settings = DocumentSetting::where('key', 'st-instruktur')->first();
        return view('programs.dokumen.st-instruktur', compact('program', 'pj', 'settings'));
    }

    public function dokumenJadwal(Program $program)
    {
        $program->load(['masterProgram', 'paketPelatihan.jenisPelatihan', 'programInstructors.instructor']);
        $unitsData = $program->selected_units_with_details;
        return view('programs.dokumen.jadwal', compact('program', 'unitsData'));
    }

    public function dokumenDaftarHadir(Program $program)
    {
        $program->load([
            'masterProgram.kejuruan',
            'paketPelatihan.jenisPelatihan',
            'participants',
            'programInstructors.instructor',
        ]);
        $settings = DocumentSetting::where('key', 'daftar-hadir')->first();
        return view('programs.dokumen.daftar-hadir', compact('program', 'settings'));
    }

    public function dokumenBiodataPeserta(Program $program)
    {
        $program->load(['masterProgram.kejuruan', 'paketPelatihan.jenisPelatihan', 'participants']);
        $settings = DocumentSetting::firstOrCreate(
        ['key' => 'biodata-peserta'],
        ['name' => 'Nominatif Peserta']
    );
        return view('programs.dokumen.biodata-peserta', compact('program', 'settings'));
    }

    public function dokumenSkPenyelenggara(Program $program)
    {
        $program->load([
            'masterProgram.kejuruan',
            'paketPelatihan.jenisPelatihan',
            'participants',
            'programInstructors.instructor',
        ]);
        $settings = DocumentSetting::where('key', 'sk-penyelenggara')->first();
        return view('programs.dokumen.sk-penyelenggara', compact('program', 'settings'));
    }

    // ========== MASTER PROGRAM ==========

    public function master(Request $request)
    {
        $query = MasterProgram::query();

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
                        $q->select('id', 'program_id');
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
            'code'                => 'required|string|max:20|unique:master_programs,code',
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'duration_hours'      => 'required|integer|min:1',
            'kejuruan_id'         => 'required|exists:kejuruans,id',
            'bidang_pelatihan_id' => 'required|exists:bidang_pelatihans,id',
            'versi'               => 'required|integer|min:1',
            'tanggal'             => 'nullable|date',
            'file_program'        => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_active'           => 'boolean',
        ]);

        $validated['program_pelatihan'] = $validated['name'];
        $validated['is_active']         = $request->has('is_active') ? 1 : 0;
        $validated['created_by']        = auth()->id();
        $validated['updated_by']        = auth()->id();

        if ($request->hasFile('file_program')) {
            $originalName = $request->file('file_program')->getClientOriginalName();
            $path         = $request->file('file_program')->storeAs('program-files', $originalName, 'public');
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
            'code'                => 'required|string|max:20|unique:master_programs,code,' . $masterProgram->id,
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'duration_hours'      => 'required|integer|min:1',
            'kejuruan_id'         => 'required|exists:kejuruans,id',
            'bidang_pelatihan_id' => 'required|exists:bidang_pelatihans,id',
            'versi'               => 'required|integer|min:1',
            'tanggal'             => 'nullable|date',
            'file_program'        => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'is_active'           => 'boolean',
        ]);

        $validated['program_pelatihan'] = $validated['name'];
        $validated['is_active']         = $request->has('is_active') ? true : false;
        $validated['updated_by']        = auth()->id();

        if ($request->hasFile('file_program')) {
            if ($masterProgram->file_program) {
                Storage::disk('public')->delete($masterProgram->file_program);
            }
            $originalName = $request->file('file_program')->getClientOriginalName();
            $path         = $request->file('file_program')->storeAs('program-files', $originalName, 'public');
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

        if ($fileExtension !== 'pdf') {
            return response()->download($path);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ========================================
    // ✅ SYNC KEMNAKER
    // ========================================

    public function syncKemnaker(Request $request)
    {
        $mode    = $request->query('mode', 'update-null');
        $phpBin  = PHP_BINARY;
        $artisan = base_path('artisan');
        $logFile = storage_path('logs/sync-kemnaker-' . date('Ymd-His') . '.log');
        $pidFile = storage_path('logs/sync-kemnaker.pid');

        $args = $mode === 'full'
            ? '--limit=100 --max-pages=999 --skip-files'
            : '--update-null --skip-files';

        if (file_exists($pidFile)) {
            $pid = trim(file_get_contents($pidFile));
            if ($this->isProcessRunning($pid)) {
                if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['status' => 'already_running']);
                }
                return redirect()->route('admin.programs.master');
            }
            @unlink($pidFile);
        }

        file_put_contents(storage_path('logs/sync-kemnaker-latest.log-path'), $logFile);

        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = "START /B \"\" \"{$phpBin}\" \"{$artisan}\" kemnaker:sync-programs {$args} >> \"{$logFile}\" 2>&1";
            pclose(popen($cmd, 'r'));
            file_put_contents($pidFile, 'windows-' . time());
        } else {
            $cmd = "\"{$phpBin}\" \"{$artisan}\" kemnaker:sync-programs {$args} >> \"{$logFile}\" 2>&1 & echo $!";
            $pid = trim(shell_exec($cmd));
            if ($pid) file_put_contents($pidFile, $pid);
        }

        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['status' => 'started']);
        }

        return redirect()->route('admin.programs.master');
    }

    public function syncStatus(Request $request)
    {
        $logPathFile = storage_path('logs/sync-kemnaker-latest.log-path');
        $pidFile     = storage_path('logs/sync-kemnaker.pid');

        if (!file_exists($logPathFile)) {
            return response()->json(['running' => false, 'log' => null]);
        }

        $logFile = trim(file_get_contents($logPathFile));

        if (!file_exists($logFile)) {
            return response()->json(['running' => true, 'log' => null]);
        }

        $log = file_get_contents($logFile);

        $isDone = str_contains($log, 'selesai!')
               || str_contains($log, 'Sync selesai')
               || str_contains($log, 'Update data null selesai');

        if ($isDone) {
            @unlink($pidFile);
            return response()->json(['running' => false, 'log' => $log]);
        }

        $running = true;
        if (file_exists($pidFile)) {
            $pid = trim(file_get_contents($pidFile));
            if (!str_starts_with($pid, 'windows-') && !$this->isProcessRunning($pid)) {
                if ((time() - filemtime($logFile)) > 30) {
                    @unlink($pidFile);
                    $running = false;
                }
            }
        }

        return response()->json(['running' => $running, 'log' => $log]);
    }

    private function isProcessRunning(string $pid): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $out = shell_exec("tasklist /FI \"PID eq {$pid}\" 2>NUL");
            return $out && str_contains($out, (string) $pid);
        }

        if (is_dir("/proc/{$pid}")) return true;
        $result = shell_exec("kill -0 {$pid} 2>&1");
        return empty($result);
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

        $units         = $query->latest()->paginate(10);
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
            'code'              => 'required|string|max:50',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
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
            'code'              => 'required|string|max:50',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
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

    public function updateUnitInMaster(Request $request, MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
    {
        $validated = $request->validate([
            'type_unit' => 'required|in:skkni,special',
            'jp'        => 'nullable|integer|min:0',
        ]);

        $masterProgram->independentCompetencyUnits()->updateExistingPivot(
            $independentCompetencyUnit->id,
            [
                'type_unit' => $validated['type_unit'],
                'jp'        => $validated['jp'] ?? 0,
            ]
        );

        return redirect()
            ->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Data unit kompetensi berhasil diperbarui!');
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
            'code'        => 'required|string|max:50|unique:independent_competency_units,code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $unit = IndependentCompetencyUnit::create($validated);

        foreach ($masterProgram->programs as $program) {
            $program->independentCompetencyUnits()->syncWithoutDetaching($unit->id);
        }

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi independen berhasil ditambahkan ke master!');
    }

    public function updateIndependentUnitInMaster(Request $request, MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:50|unique:independent_competency_units,code,' . $independentCompetencyUnit->id,
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $independentCompetencyUnit->update($validated);

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi independen berhasil diperbarui!');
    }

    public function destroyIndependentUnitInMaster(MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
    {
        foreach ($masterProgram->programs as $program) {
            $program->independentCompetencyUnits()->detach($independentCompetencyUnit->id);
        }

        $independentCompetencyUnit->delete();

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi independen berhasil dihapus!');
    }

    public function storeUnitToMaster(Request $request, MasterProgram $masterProgram)
    {
        $validated = $request->validate([
            'independent_competency_unit_id' => 'required|exists:independent_competency_units,id',
            'type_unit'                      => 'required|in:skkni,special',
            'jp'                             => 'nullable|integer|min:0',
        ]);

        $pivotId = (string) \Illuminate\Support\Str::uuid();

        $masterProgram->independentCompetencyUnits()->attach(
            $validated['independent_competency_unit_id'],
            [
                'id'        => $pivotId,
                'type_unit' => $validated['type_unit'],
                'jp'        => $validated['jp'] ?? 0,
            ]
        );

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi berhasil ditambahkan ke master program!');
    }

    public function destroyUnitInMaster(MasterProgram $masterProgram, IndependentCompetencyUnit $independentCompetencyUnit)
    {
        \DB::table('program_pelatihan_units')
            ->where('master_programs_id', $masterProgram->id)
            ->where('independent_competency_units_id', $independentCompetencyUnit->id)
            ->delete();

        return redirect()->route('admin.programs.master.show', $masterProgram)
            ->with('success', 'Unit kompetensi berhasil dihapus!');
    }

    public function storeForPaket(Request $request, PaketPelatihan $paket)
    {
        $validated = $request->validate([
            'master_program_id'              => 'required|exists:master_programs,id',
            'batch'                          => 'required|integer|min:1',
            'angkatan'                       => 'nullable|string',
            'independent_competency_unit_id' => 'nullable|exists:independent_competency_units,id',
            'start_date'                     => 'required|date',
            'end_date'                       => 'required|date|after_or_equal:start_date',
            'max_participants'               => 'nullable|integer|min:1',
            'ada_industri'                   => ['nullable', Rule::in(['Y', 'N'])],
            'jp_harian'                      => 'nullable|integer|min:0',
            'jp'                             => 'nullable|integer|min:0',
        ]);

        $validated['paket_pelatihan_id'] = $paket->id;
        // ✅ Status otomatis dihitung dari tanggal
        $validated['status'] = $this->calculateStatus($validated['start_date'], $validated['end_date']);

        $program = Program::create($validated);

        return redirect()->back()->with('success', 'Program berhasil ditambahkan ke paket pelatihan.');
    }

    public function destroyForPaket(PaketPelatihan $paket, Program $program)
    {
        if ($program->paket_pelatihan_id !== $paket->id) {
            abort(403);
        }

        $program->delete();

        return redirect()->back()->with('success', 'Program berhasil dihapus dari paket.');
    }

    public function editTemplate(Program $program, $template)
    {
        $templateNames = [
            'sk-peserta'       => 'SPT Peserta',
            'sk-penyelenggara' => 'SK Penyelenggara',
            'st-instruktur'    => 'ST Instruktur',
            'jadwal'           => 'Jadwal Pelatihan',
            'daftar-hadir'     => 'Daftar Hadir',
            'biodata-peserta'  => 'Biodata Peserta',
        ];

        if (!isset($templateNames[$template])) {
            abort(404, 'Template tidak ditemukan');
        }

        $settings = DocumentSetting::firstOrCreate(
            ['key' => $template],
            array_merge(
                ['name' => $templateNames[$template]],
                DocumentSetting::getDefaults($template)
            )
        );

        return view('programs.dokumen.edit-template', compact('program', 'template', 'settings'));
    }

    public function updateTemplate(Program $program, $template, Request $request)
    {
        $validated = $request->validate([
            'dasar_hukum_1' => 'nullable|string',
            'dasar_hukum_2' => 'nullable|string',
            'dasar_hukum_3' => 'nullable|string',
            'kop_surat'     => 'nullable|string',
            'format_nomor'  => 'nullable|string',
            'tempat_surat'  => 'nullable|string',
            'ttd_pengirim'  => 'nullable|string',
            'nama_pengirim' => 'nullable|string',
            'nip_pengirim'  => 'nullable|string',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $settings = DocumentSetting::where('key', $template)->first();

        if (!$settings) {
            return back()->with('error', 'Setting tidak ditemukan');
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $path = $request->file('logo')->store('document-logos', 'public');
            $validated['logo_path'] = $path;
        }

        $settings->update($validated);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Pengaturan dokumen berhasil diperbarui!');
    }

    // ========================================
    // ✅ HELPER: HITUNG STATUS OTOMATIS DARI TANGGAL
    // ========================================

    /**
     * Hitung status berdasarkan tanggal mulai dan selesai.
     *
     * Logika:
     *  - start_date > hari ini          → planned
     *  - start_date <= hari ini <= end_date → ongoing
     *  - end_date < hari ini            → completed
     */
    private function calculateStatus(string $startDate, string $endDate): string
    {
        $today = now()->toDateString();

        if ($startDate > $today) {
            return 'planned';
        }

        if ($endDate < $today) {
            return 'completed';
        }

        return 'ongoing';
    }

    /**
     * Label status dalam Bahasa Indonesia.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'planned'   => 'Rencana',
            'ongoing'   => 'Berjalan',
            'completed' => 'Selesai',
            default     => ucfirst($status),
        };
    }

    // ========================================
    // ✅ SINKRONISASI STATUS SEMUA PROGRAM
    // ========================================

    /**
     * Sinkronisasi status program berdasarkan tanggal saat ini.
     *
     * Transisi yang ditangani:
     *  1. planned  → ongoing   : start_date <= hari ini <= end_date
     *  2. ongoing  → completed : end_date < hari ini
     *  3. ongoing  → planned   : start_date > hari ini (tanggal diundur)
     *  4. planned  → completed : end_date < hari ini (tanggal diundur melewati hari ini)
     *
     * @param int|null $programId — null = sinkronisasi semua, int = hanya program tertentu
     */
    private function autoSyncStatuses(?int $programId = null): void
    {
        $today = now()->toDateString();

        // Helper closure untuk membuat query dasar
        $baseQuery = function() use ($programId) {
            $q = Program::query();
            if ($programId !== null) {
                $q->where('id', $programId);
            }
            return $q;
        };

        // 1. planned → ongoing  (tanggal mulai sudah tiba, belum selesai)
        (clone $baseQuery())
            ->where('status', 'planned')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->update(['status' => 'ongoing']);

        // 2. ongoing/planned → completed  (tanggal selesai sudah lewat)
        (clone $baseQuery())
            ->whereIn('status', ['planned', 'ongoing'])
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'completed']);

        // 3. ongoing → planned  (tanggal mulai diundur ke masa depan)
        (clone $baseQuery())
            ->where('status', 'ongoing')
            ->whereDate('start_date', '>', $today)
            ->update(['status' => 'planned']);

        // 4. completed → ongoing  (tanggal diperpanjang / end_date diperpanjang ke masa depan)
        (clone $baseQuery())
            ->where('status', 'completed')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->update(['status' => 'ongoing']);

        // 5. completed → planned  (start_date & end_date keduanya dipindah ke masa depan)
        (clone $baseQuery())
            ->where('status', 'completed')
            ->whereDate('start_date', '>', $today)
            ->update(['status' => 'planned']);
    }

        public function publicIndex(Request $request)
{
    $query = MasterProgram::where('is_active', true)
        ->orderBy('name');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $masterPrograms = $query->paginate(12);

    return view('programs.public', compact('masterPrograms'));
}
}
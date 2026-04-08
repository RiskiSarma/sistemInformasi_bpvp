<?php

namespace App\Http\Controllers;

use App\Models\PengajarEksternal;
use App\Models\Pendidikan;
use App\Models\PaketPelatihanPengajarProgram;
use App\Models\User;
use App\Models\JenisMateriPelatihan;
use Illuminate\Support\Facades\Log;
use App\Models\PaketPelatihanPengajarSubUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajarEksternalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $pengajars = PengajarEksternal::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('instansi', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // VARIABEL YANG DIBUTUHKAN UNTUK MODAL EDIT & ASSIGN
        $jenisMateriList      = JenisMateriPelatihan::orderBy('jenis_materi_pelatihan')->get();
        $instructorList       = User::where('role', 'instructor')->get(); // atau model Instructor kamu
        $pengajarEksternalList = PengajarEksternal::orderBy('nama')->get();
        $pendidikans          = Pendidikan::all(); // jika kamu punya model Pendidikan

        return view('pengajar-eksternal.index', compact(
            'pengajars',
            'jenisMateriList',
            'instructorList',
            'pengajarEksternalList',
            'pendidikans'
        ));
    }

    public function create()
    {
        $pendidikans = Pendidikan::orderBy('pendidikan')->get();
        return view('pengajar-eksternal.create', compact('pendidikans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|string|max:100|unique:pengajar_eksternal,nik',
            'nip' => 'required|string|max:100|unique:pengajar_eksternal,nip',
            'instansi' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:pengajar_eksternal,email',
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'kejuruan_pendidikan' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama harus diisi',
            'nik.required' => 'NIK harus diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'nip.required' => 'NIP harus diisi.',
            'nip.unique'   => 'NIP sudah terdaftar di sistem.',
            'instansi.required' => 'Instansi harus diisi',
            'telepon.required' => 'Telepon harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'email.email' => 'Format email tidak valid',
        ]);

        PengajarEksternal::create($validated);

        return redirect()->route('admin.pengajar-eksternal.index')
            ->with('success', 'Pengajar eksternal berhasil ditambahkan!');
    }

    public function show(PengajarEksternal $pengajarEksternal)
{
    $pengajarEksternal->load([
        'pendidikan',
        'user',
        'programAssignments' => function ($query) {
            $query->with(['program.masterProgram', 'jenisMateri']);
        },
        'subUnitAssignments' => function ($query) {
            $query->with([
                'program.masterProgram',
                'paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit',
            ]);
        }
    ]);

    // === LOAD SEMUA ASSIGNMENT SUB UNIT (agar tampil semua pengajar) ===
    $subUnitIds = $pengajarEksternal->subUnitAssignments->pluck('pp_unit_id');

    $allSubUnitAssignments = collect();

    if ($subUnitIds->isNotEmpty()) {
        $allSubUnitAssignments = PaketPelatihanPengajarSubUnit::whereIn('pp_unit_id', $subUnitIds)
            ->with([
                'program.masterProgram',
                'paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit',
                'pengajarEksternalData',   // relasi ke PengajarEksternal
                'pengajarInternal'         // relasi ke Instructor / User
            ])
            ->get();
    }

    return view('pengajar-eksternal.show', compact(
        'pengajarEksternal', 
        'allSubUnitAssignments'
    ));
}

    public function edit(PengajarEksternal $pengajarEksternal)
    {
        $pendidikans = Pendidikan::orderBy('pendidikan')->get();
        return view('pengajar-eksternal.edit', compact('pengajarEksternal', 'pendidikans'));
    }

    public function update(Request $request, PengajarEksternal $pengajarEksternal)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|string|max:100|unique:pengajar_eksternal,nik,' . $pengajarEksternal->id,
            'nip' => 'required|string|max:100|unique:pengajar_eksternal,nip,' . $pengajarEksternal->id,
            'instansi' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|max:100|unique:pengajar_eksternal,email,' . $pengajarEksternal->id,
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'kejuruan_pendidikan' => 'nullable|string|max:100',
        ], [
            'nama.required' => 'Nama harus diisi',
            'nik.required' => 'NIK harus diisi',
            'nip.required' => 'NIP harus diisi.',
            'nip.unique'   => 'NIP sudah terdaftar di sistem.',
            'instansi.required' => 'Instansi harus diisi',
            'telepon.required' => 'Telepon harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $pengajarEksternal->update($validated);

        return redirect()->route('admin.pengajar-eksternal.index')
            ->with('success', 'Data pengajar eksternal berhasil diperbarui!');
    }

    public function destroy(PengajarEksternal $pengajarEksternal)
    {
        $pengajarEksternal->delete();
        
        return redirect()->route('admin.pengajar-eksternal.index')
            ->with('success', 'Pengajar eksternal berhasil dihapus!');
    }

    // ========================================
    // ✅ METHOD BARU: ASSIGN TO PROGRAM
    // ========================================
    /**
     * Assign pengajar eksternal ke program dengan jenis materi tertentu
     * Dipanggil dari modal di halaman pengajar-eksternal/index
     */
    public function assignToProgram(Request $request, PengajarEksternal $pengajarEksternal)
    {
        $validated = $request->validate([
            'programs_id' => 'required|exists:programs,id',
            'jenis_materi_pelatihan_id' => 'required|exists:jenis_materi_pelatihan,id',
            'pengajar_tipe' => 'required|in:eksternal',
        ], [
            'programs_id.required' => 'Program harus dipilih',
            'jenis_materi_pelatihan_id.required' => 'Jenis materi harus dipilih',
        ]);

        try {
            DB::beginTransaction();

            // Cek duplikasi
            $exists = PaketPelatihanPengajarProgram::where('programs_id', $validated['programs_id'])
                ->where('jenis_materi_pelatihan_id', $validated['jenis_materi_pelatihan_id'])
                ->where('pengajar_eksternal_id', $pengajarEksternal->id)
                ->where('pengajar_eksternal', 'Y')
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Pengajar sudah di-assign ke program dan jenis materi ini!');
            }

            PaketPelatihanPengajarProgram::create([
                'programs_id' => $validated['programs_id'],
                'jenis_materi_pelatihan_id' => $validated['jenis_materi_pelatihan_id'],
                'pengajar_eksternal_id' => $pengajarEksternal->id,
                'pengajar_eksternal' => 'Y',
                'pengajar_internal_id' => null,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('admin.pengajar-eksternal.index')
                ->with('success', 'Pengajar berhasil di-assign ke program!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal assign pengajar: ' . $e->getMessage());
        }
    }

    // ========================================
    // ✅ METHOD BARU: GET ASSIGNMENTS (AJAX)
    // ========================================
    /**
     * Fetch assignments pengajar untuk ditampilkan di modal
     * Dipanggil via AJAX dari Alpine.js
     */
    public function getAssignments(PengajarEksternal $pengajarEksternal)
    {
        $assignments = PaketPelatihanPengajarProgram::where('pengajar_eksternal_id', $pengajarEksternal->id)
            ->where('pengajar_eksternal', 'Y')
            ->with([
                'program.masterProgram',
                'program.paketPelatihan.jenisPelatihan',
                'jenisMateri'
            ])
            ->get()
            ->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'program_id' => $assignment->programs_id,
                    'program_name' => $assignment->program->masterProgram->name ?? 'Program',
                    'jenis_pelatihan' => $assignment->program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-',
                    'angkatan' => $assignment->program->angkatan 
                        ? 'Angkatan ' . $assignment->program->angkatan 
                        : null,
                    'jenis_materi' => $assignment->jenisMateri->jenis_materi_pelatihan ?? '-',
                ];
            });

        return response()->json($assignments);
    }
    /**
     * ✅ GET DETAIL UNTUK MODAL (JSON RESPONSE) - FIXED VERSION
     */
    public function getDetail(PengajarEksternal $pengajarEksternal)
{
    try {
        $pengajarEksternal->load(['pendidikan', 'user']);

        // Langkah 1: Ambil SEMUA programs_id yang pernah di-assign ke pengajar eksternal ini
        $relatedProgramIds = PaketPelatihanPengajarProgram::where('pengajar_eksternal_id', $pengajarEksternal->id)
            ->where('pengajar_eksternal', 'Y')
            ->pluck('programs_id')
            ->unique()
            ->toArray();

        // Jika tidak ada program terkait, kembalikan array kosong
        if (empty($relatedProgramIds)) {
            $programAssignments = collect([]);
        } else {
            // Langkah 2: Ambil SEMUA assignment (internal + eksternal) di semua program tersebut
            $programAssignments = PaketPelatihanPengajarProgram::query()
    ->with([
        'program' => function ($q) {
            $q->select('id', 'master_program_id', 'angkatan')
              ->with('masterProgram:id,name');
        },
        'jenisMateri:id,jenis_materi_pelatihan',
        'pengajarEksternalData:id,nama,instansi',
        'pengajarInternal:id,name'
    ])
    ->get()
    ->map(function ($assignment) {
        $isEksternal = $assignment->pengajar_eksternal === 'Y';

        $namaPengajar = $isEksternal
            ? ($assignment->pengajarEksternalData?->nama ?? '-')
            : ($assignment->pengajarInternal?->name ?? '-');

        $instansi = $isEksternal
            ? ($assignment->pengajarEksternalData?->instansi ?? '-')
            : 'BPVP Banda Aceh';

        return [
            'id'                  => $assignment->id,
            'programs_id'         => $assignment->programs_id,
            'program_name'        => $assignment->program?->masterProgram?->name ?? '(Program tidak ditemukan)',
            'angkatan'            => $assignment->program?->angkatan ?? '-',
            'jenis_materi'        => $assignment->jenisMateri?->jenis_materi_pelatihan ?? '-',
            'jenis_materi_id'     => $assignment->jenis_materi_pelatihan_id,
            'tipe'                => $isEksternal ? 'Eksternal' : 'Internal',
            'nama_pengajar'       => $namaPengajar,
            'instansi'            => $instansi,
            'pengajar_internal_id'   => $assignment->pengajar_internal_id,
            'pengajar_eksternal_id'  => $assignment->pengajar_eksternal_id,
        ];
    });
        }

        // SUB UNIT ASSIGNMENTS - PAKSA TAMPIL SEMUA DATA (UNTUK DEBUG SAJA)
$subUnitAssignments = PaketPelatihanPengajarSubUnit::with([
    'paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit:id,name',
    'program.masterProgram:id,name',
    'pengajarEksternal:id,nama,instansi',
    'pengajarInternal:id,name'
])
->get()
->map(function ($sub) {
    $isEksternal = $sub->pengajar_eksternal === 'Y';

    $namaPengajar = $isEksternal
        ? ($sub->pengajarEksternal?->nama ?? 'Tidak Diketahui')
        : ($sub->pengajarInternal?->name ?? 'Tidak Diketahui');

    $instansi = $isEksternal
        ? ($sub->pengajarEksternal?->instansi ?? '-')
        : 'BPVP Banda Aceh';

    $paketUnitName = $sub->paketPelatihanUnit?->programPelatihanUnit?->independentCompetencyUnit?->name 
                     ?? ($sub->pp_unit_id ? 'Unit #' . $sub->pp_unit_id : 'Unit Tidak Diketahui');

    $programName = $sub->program?->masterProgram?->name 
                   ?? ($sub->programs_id ? 'Program #' . $sub->programs_id : 'Program Tidak Diketahui');

    return [
        'id'                      => $sub->id,
        'pp_unit_id'              => $sub->pp_unit_id,
        'programs_id'             => $sub->programs_id,
        'paket_unit_name'         => $paketUnitName,
        'program_name'            => $programName,
        'tipe'                    => $isEksternal ? 'Eksternal' : 'Internal',
        'nama_pengajar'           => $namaPengajar,
        'instansi'                => $instansi,
        'pengajar_internal_id'    => $sub->pengajar_internal_id,
        'pengajar_eksternal_id'   => $sub->pengajar_eksternal_id,
    ];
});

        return response()->json([
            'id'                  => $pengajarEksternal->id,
            'nama'                => $pengajarEksternal->nama,
            'nik'                 => $pengajarEksternal->nik,
            'nip'                 => $pengajarEksternal->nip,
            'instansi'            => $pengajarEksternal->instansi,
            'jabatan'             => $pengajarEksternal->jabatan ?? '-',
            'alamat'              => $pengajarEksternal->alamat ?? '-',
            'telepon'             => $pengajarEksternal->telepon,
            'email'               => $pengajarEksternal->email,
            'pendidikan'          => $pengajarEksternal->pendidikan?->pendidikan ?? '-',
            'kejuruan'            => $pengajarEksternal->kejuruan_pendidikan ?? '-',
            'programAssignments'  => $programAssignments,
            'subUnitAssignments'  => $subUnitAssignments,
        ]);

    } catch (\Exception $e) {
        \Log::error("Error getDetail pengajar {$pengajarEksternal->id}: {$e->getMessage()}");
        \Log::error($e->getTraceAsString());

        return response()->json(['error' => 'Gagal memuat detail', 'message' => $e->getMessage()], 500);
    }

}

    /**
     * ASSIGN PENGAJAR EKSTERNAL KE PROGRAM (JENIS MATERI)
     */
    public function assignProgram(Request $request, PengajarEksternal $pengajarEksternal)
    {
        $validated = $request->validate([
            'programs_id' => 'required|exists:programs,id',
            'jenis_materi_pelatihan_id' => 'required|exists:jenis_materi_pelatihan,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'required_if:pengajar_tipe,internal|nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'required_if:pengajar_tipe,eksternal|nullable|exists:pengajar_eksternal,id',
        ], [
            'programs_id.required' => 'Program harus dipilih',
            'jenis_materi_pelatihan_id.required' => 'Jenis materi harus dipilih',
        ]);

        try {
            DB::beginTransaction();

            // Tentukan pengajar berdasarkan tipe
            if ($validated['pengajar_tipe'] === 'eksternal') {
                $validated['pengajar_eksternal_id'] = $pengajarEksternal->id;
                $validated['pengajar_internal_id'] = null;
            }

            // Cek duplikasi
            $exists = PaketPelatihanPengajarProgram::where('programs_id', $validated['programs_id'])
                ->where('jenis_materi_pelatihan_id', $validated['jenis_materi_pelatihan_id'])
                ->where(function($q) use ($validated) {
                    if ($validated['pengajar_tipe'] === 'eksternal') {
                        $q->where('pengajar_eksternal_id', $validated['pengajar_eksternal_id'])
                          ->where('pengajar_eksternal', 'Y');
                    } else {
                        $q->where('pengajar_internal_id', $validated['pengajar_internal_id'])
                          ->where('pengajar_eksternal', 'N');
                    }
                })
                ->exists();

            if ($exists) {
                return back()->with('error', 'Pengajar ini sudah di-assign ke program dan jenis materi yang sama!');
            }

            // Create assignment
            PaketPelatihanPengajarProgram::create([
                'programs_id' => $validated['programs_id'],
                'jenis_materi_pelatihan_id' => $validated['jenis_materi_pelatihan_id'],
                'pengajar_eksternal' => $validated['pengajar_tipe'] === 'eksternal' ? 'Y' : 'N',
                'pengajar_eksternal_id' => $validated['pengajar_eksternal_id'],
                'pengajar_internal_id' => $validated['pengajar_internal_id'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Pengajar berhasil di-assign ke program!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error assigning pengajar eksternal: ' . $e->getMessage());
            return back()->with('error', 'Gagal assign pengajar: ' . $e->getMessage());
        }
    }

    /**
     * ASSIGN PENGAJAR EKSTERNAL KE SUB UNIT
     */
    public function assignSubUnit(Request $request, PengajarEksternal $pengajarEksternal)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'programs_id' => 'required|exists:programs,id',
            'pengajar_tipe' => 'required|in:internal,eksternal',
            'pengajar_internal_id' => 'required_if:pengajar_tipe,internal|nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'required_if:pengajar_tipe,eksternal|nullable|exists:pengajar_eksternal,id',
        ], [
            'paket_pelatihan_unit_id.required' => 'Paket unit harus dipilih',
            'programs_id.required' => 'Program harus dipilih',
        ]);

        try {
            DB::beginTransaction();

            // Tentukan pengajar berdasarkan tipe
            if ($validated['pengajar_tipe'] === 'eksternal') {
                $validated['pengajar_eksternal_id'] = $pengajarEksternal->id;
                $validated['pengajar_internal_id'] = null;
            }

            // Cek duplikasi
            $exists = PaketPelatihanPengajarSubUnit::where('paket_pelatihan_unit_id', $validated['paket_pelatihan_unit_id'])
                ->where('programs_id', $validated['programs_id'])
                ->where(function($q) use ($validated) {
                    if ($validated['pengajar_tipe'] === 'eksternal') {
                        $q->where('pengajar_eksternal_id', $validated['pengajar_eksternal_id'])
                          ->where('pengajar_eksternal', 'Y');
                    } else {
                        $q->where('pengajar_internal_id', $validated['pengajar_internal_id'])
                          ->where('pengajar_eksternal', 'N');
                    }
                })
                ->exists();

            if ($exists) {
                return back()->with('error', 'Pengajar ini sudah di-assign ke paket unit yang sama!');
            }

            // Create assignment
            PaketPelatihanPengajarSubUnit::create([
                'paket_pelatihan_unit_id' => $validated['paket_pelatihan_unit_id'],
                'programs_id' => $validated['programs_id'],
                'pengajar_eksternal' => $validated['pengajar_tipe'] === 'eksternal' ? 'Y' : 'N',
                'pengajar_eksternal_id' => $validated['pengajar_eksternal_id'],
                'pengajar_internal_id' => $validated['pengajar_internal_id'],
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            return back()->with('success', 'Pengajar berhasil di-assign ke sub unit!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error assigning pengajar to sub unit: ' . $e->getMessage());
            return back()->with('error', 'Gagal assign pengajar ke sub unit: ' . $e->getMessage());
        }
    }
        /**
     * Tampilkan Jadwal Pengajar Eksternal (mirip Instructor)
     */
        /**
     * Tampilkan Jadwal Pengajar Eksternal
     */
    public function schedule(PengajarEksternal $pengajarEksternal)
    {
        // Cek apakah model punya relasi schedules
        $schedules = collect(); // default kosong

        if (method_exists($pengajarEksternal, 'schedules')) {
            $pengajarEksternal->load(['schedules' => function($q) {
                $q->with('program.masterProgram')
                  ->where('is_active', true)
                  ->orderBy('day_of_week')
                  ->orderBy('start_time');
            }]);
            $schedules = $pengajarEksternal->schedules ?? collect();
        }

        $days = [
            'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
            'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 
            'sunday' => 'Minggu'
        ];

        return view('pengajar-eksternal.schedule', compact(
            'pengajarEksternal',
            'schedules',
            'days'
        ));
    }

    /**
     * Tampilkan Absensi Pengajar Eksternal
     */
    public function attendance(PengajarEksternal $pengajarEksternal)
    {
        // Untuk sementara tampilkan halaman kosong / placeholder
        // Nanti bisa kamu kembangkan mirip InstructorAttendanceController

        return view('pengajar-eksternal.attendance', compact('pengajarEksternal'));
    }
}
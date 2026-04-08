<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\IndependentUnitController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\JenisPelatihanController;
use App\Http\Controllers\PaketPelatihanController;
use App\Http\Controllers\KejuruanBidangController;
use App\Http\Controllers\ProgramUnitController;
use App\Http\Controllers\PaketUnitController;
use App\Http\Controllers\PaketSubUnitController;
use App\Http\Controllers\PengajarEksternalController;
use App\Http\Controllers\JenisMateriPelatihanController;
use App\Http\Controllers\PengajarAssignmentController;
use App\Http\Controllers\PendidikanController;
use App\Http\Controllers\InstructorAttendanceController;
use App\Http\Controllers\PaketPengajarProgramController;
use App\Http\Controllers\PaketPengajarSubUnitController;



use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard berdasarkan role
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isInstructor()) {
        return redirect()->route('instructor.dashboard');
    } else {
        return redirect()->route('participant.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// ============================================
// ADMIN ROUTES
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Program Pelatihan
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [ProgramController::class, 'index'])->name('index');
        Route::get('/create', [ProgramController::class, 'create'])->name('create');
        Route::post('/', [ProgramController::class, 'store'])->name('store');
        Route::get('/{program}/edit', [ProgramController::class, 'edit'])->name('edit');
        Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');
        
         Route::prefix('{program}')->group(function () {
            Route::resource('units', ProgramUnitController::class)->names([
                'index' => 'units.index',
                'create' => 'units.create',
                'store' => 'units.store',
                'show' => 'units.show',
                'edit' => 'units.edit',
                'update' => 'units.update',
                'destroy' => 'units.destroy',
            ]);
            
    
            // ✅ PAKET UNITS (TAMBAHKAN DI SINI - TANPA prefix {program} lagi!)
            Route::post('paket-units', [PaketUnitController::class, 'store'])->name('paket-units.store');
            Route::put('paket-units/{paketUnit}', [PaketUnitController::class, 'update'])->name('paket-units.update');
            Route::delete('paket-units/{paketUnit}', [PaketUnitController::class, 'destroy'])->name('paket-units.destroy');

            // ✅ PAKET SUB-UNITS
            Route::post('paket-sub-units', [PaketSubUnitController::class, 'store'])->name('paket-sub-units.store');
            Route::put('paket-sub-units/{paketSubUnit}', [PaketSubUnitController::class, 'update'])->name('paket-sub-units.update');
            Route::delete('paket-sub-units/{paketSubUnit}', [PaketSubUnitController::class, 'destroy'])->name('paket-sub-units.destroy');
        });

        // Master Program
        Route::get('/master', [ProgramController::class, 'master'])->name('master');
        Route::post('/master', [ProgramController::class, 'storeMaster'])->name('master.store');
        Route::get('/master/{masterProgram}', [ProgramController::class, 'showMaster'])->name('master.show');
        Route::get('/master/{masterProgram}/edit', [ProgramController::class, 'editMaster'])->name('master.edit');
        Route::put('/master/{masterProgram}', [ProgramController::class, 'updateMaster'])->name('master.update');
        Route::delete('/master/{masterProgram}', [ProgramController::class, 'destroyMaster'])->name('master.destroy');
        // Di dalam group Master Program
        Route::get('/master/{masterProgram}/preview-file', [ProgramController::class, 'previewFile'])->name('master.preview-file');
        
        // Sync Kemnaker - PINDAHKAN KE SINI
        Route::get('/sync-kemnaker', [ProgramController::class, 'syncKemnaker'])->name('sync-kemnaker');
        // routes/web.php — di dalam group admin
        Route::get('/sync-status', [ProgramController::class, 'syncStatus'])->name('sync-status');
        
        // CRUD Unit Kompetensi Independen di Master Program (INI YANG BENAR & BERSIH)
        Route::prefix('master/{masterProgram}/units')->name('master.units.')->group(function () {
            Route::post('/', [ProgramController::class, 'storeUnitToMaster'])->name('store');
            Route::put('/{independentCompetencyUnit}', [ProgramController::class, 'updateUnitInMaster'])->name('update');
            Route::delete('/{independentCompetencyUnit}', [ProgramController::class, 'destroyUnitInMaster'])->name('destroy');
        });

        Route::resource('batches', BatchController::class)->names([
            'index'   => 'batches.index',
            'create'  => 'batches.create',
            'store'   => 'batches.store',
            'show'    => 'batches.show',
            'edit'    => 'batches.edit',
            'update'  => 'batches.update',
            'destroy' => 'batches.destroy',
        ]);

        Route::prefix('jenis-pelatihan')->name('jenis-pelatihan.')->group(function () {
            Route::get('/', [JenisPelatihanController::class, 'index'])->name('index');
            Route::post('/', [JenisPelatihanController::class, 'store'])->name('store');
            Route::put('/{jenis}', [JenisPelatihanController::class, 'update'])->name('update');
            Route::delete('/{jenis}', [JenisPelatihanController::class, 'destroy'])->name('destroy');
        });
        
        // Kejuruan & Bidang (gabungan)
        Route::prefix('kejuruan-bidang')->name('kejuruan-bidang.')->group(function () {
            Route::get('/', [KejuruanBidangController::class, 'index'])->name('index');

             // ✅ TAMBAHKAN BARIS INI (ROUTE BARU)
            Route::post('/sync-kejuruan', [KejuruanBidangController::class, 'syncKejuruan'])->name('sync-kejuruan');
            // Kejuruan CRUD
            Route::post('/', [KejuruanBidangController::class, 'storeKejuruan'])->name('kejuruan.store');
            Route::put('/kejuruan/{kejuruan}', [KejuruanBidangController::class, 'updateKejuruan'])->name('kejuruan.update');
            Route::delete('/kejuruan/{kejuruan}', [KejuruanBidangController::class, 'destroyKejuruan'])->name('kejuruan.destroy');

            // Bidang CRUD
            Route::post('/bidang', [KejuruanBidangController::class, 'storeBidang'])->name('bidang.store');
            Route::put('/bidang/{bidang}', [KejuruanBidangController::class, 'updateBidang'])->name('bidang.update');
            Route::delete('/bidang/{bidang}', [KejuruanBidangController::class, 'destroyBidang'])->name('bidang.destroy');
        });

        Route::prefix('paket-pelatihan')->name('paket-pelatihan.')->group(function () {
        // Route utama paket pelatihan
        Route::get('/', [PaketPelatihanController::class, 'index'])->name('index');
        Route::post('/', [PaketPelatihanController::class, 'store'])->name('store');
        Route::put('/{paket}', [PaketPelatihanController::class, 'update'])->name('update');
        Route::delete('/{paket}', [PaketPelatihanController::class, 'destroy'])->name('destroy');

        // ✅ ROUTES UNTUK PAKET UNITS & SUB UNITS (DIPERBAIKI)
        Route::prefix('{paket}')->group(function () {
            // Paket Units
            Route::get('paket-units', [PaketUnitController::class, 'getUnits'])->name('paket-units.data');
            Route::post('paket-units', [PaketUnitController::class, 'store'])->name('paket-units.store');
            Route::put('paket-units/{paketUnit}', [PaketUnitController::class, 'update'])->name('paket-units.update');
            Route::delete('paket-units/{paketUnit}', [PaketUnitController::class, 'destroy'])->name('paket-units.destroy');

            // ✅ PAKET SUB UNITS (NAMA ROUTE DIPERBAIKI - SESUAI DENGAN FORM ACTION)
            Route::get('paket-sub-units', [PaketSubUnitController::class, 'getSubUnits'])->name('paket-sub-units.data');
            Route::post('paket-sub-units', [PaketSubUnitController::class, 'store'])->name('paket-sub-units.store');
            Route::put('paket-sub-units/{paketSubUnit}', [PaketSubUnitController::class, 'update'])->name('paket-sub-units.update');
            Route::delete('paket-sub-units/{paketSubUnit}', [PaketSubUnitController::class, 'destroy'])->name('paket-sub-units.destroy');
        });
    });
        

        // Unit Kompetensi
        Route::get('/units', [ProgramController::class, 'units'])->name('units');
        Route::post('/units', [ProgramController::class, 'storeUnit'])->name('units.store');
        Route::get('/units/{unit}', [ProgramController::class, 'showUnit'])->name('units.show');
        Route::get('/units/{unit}/edit', [ProgramController::class, 'editUnit'])->name('units.edit');
        Route::put('/units/{unit}', [ProgramController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit}', [ProgramController::class, 'destroyUnit'])->name('units.destroy');
        Route::get('/{program}', [ProgramController::class, 'show'])->name('show');

        // ✅ [2] ROUTES EDIT, UPDATE, DELETE (sebelum /{program}/dokumen & /{program} show)
        Route::get('/{program}/edit', [ProgramController::class, 'edit'])->name('edit');
        Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');

        // ✅ [3] DOKUMEN ADMINISTRASI — HARUS SEBELUM /{program} SHOW
        Route::prefix('/{program}/dokumen')->name('dokumen.')->group(function () {
            Route::get('/sk-peserta',      [ProgramController::class, 'dokumenSkPeserta'])->name('sk-peserta');
            Route::get('/st-instruktur',   [ProgramController::class, 'dokumenStInstruktur'])->name('st-instruktur');
            Route::get('/jadwal',          [ProgramController::class, 'dokumenJadwal'])->name('jadwal');
            Route::get('/daftar-hadir',    [ProgramController::class, 'dokumenDaftarHadir'])->name('daftar-hadir');
            Route::get('/biodata-peserta', [ProgramController::class, 'dokumenBiodataPeserta'])->name('biodata-peserta');
            Route::get('/sk-penyelenggara',     [ProgramController::class, 'dokumenSkPenyelenggara'])->name('sk-penyelenggara');
            Route::get('/edit/{template}', [ProgramController::class, 'editTemplate'])->name('edit-template');
    Route::put('/update/{template}', [ProgramController::class, 'updateTemplate'])->name('update-template');
        });
    });

        // Route::prefix('programs/{program}')->group(function () {
        // Route::resource('units', ProgramUnitController::class); // untuk program_pelatihan_units (nested di master program)
        // Route::resource('paket-units', PaketUnitController::class); // untuk paket_pelatihan_units
        // Route::resource('paket-sub-units', PaketSubUnitController::class); // untuk paket_pelatihan_sub_units
        // Route::resource('paket-pengajar-programs', PaketPengajarProgramController::class); // untuk paket_pelatihan_pengajar_programs
        // Route::resource('paket-pengajar-sub-units', PaketPengajarSubUnitController::class); // untuk paket_pelatihan_pengajar_sub_units
    
    // Peserta
    // Import Excel
    Route::get('participants/import', [ParticipantController::class, 'importForm'])
        ->name('participants.import.form');
    Route::post('participants/import', [ParticipantController::class, 'import'])
        ->name('participants.import');

    // Download Template Excel
    Route::get('participants/template', [ParticipantController::class, 'downloadTemplate'])
        ->name('participants.template');

    // Resource routes (index, create, store, show, edit, update, destroy)
    Route::resource('participants', ParticipantController::class)->names([
        'index'   => 'participants.index',
        'create'  => 'participants.create',
        'store'   => 'participants.store',
        'show'    => 'participants.show',
        'edit'    => 'participants.edit',
        'update'  => 'participants.update',
        'destroy' => 'participants.destroy',
    ]);
    Route::resource('participants', ParticipantController::class);
    
    // Instruktur
    Route::resource('instructors', InstructorController::class);
    Route::get('/instructors/{instructor}/schedule', [InstructorController::class, 'schedule'])->name('instructors.schedule');
    // Absensi Instruktur (Admin)
    Route::get('/instructors/{instructor}/attendance', [InstructorAttendanceController::class, 'show'])
    ->name('instructors.attendance');
    Route::post('/instructors/{instructor}/attendance', [InstructorAttendanceController::class, 'store'])
        ->name('instructors.attendance.store');
    Route::put('/instructors/attendance/{attendance}', [InstructorAttendanceController::class, 'update'])
        ->name('instructors.attendance.update');
    Route::delete('/instructors/attendance/{attendance}', [InstructorAttendanceController::class, 'destroy'])
        ->name('instructors.attendance.destroy');

// ========================================
    // PENGAJAR EKSTERNAL ROUTES
    // ========================================
    Route::prefix('pengajar-eksternal')->name('pengajar-eksternal.')->group(function () {
        Route::get('/', [PengajarEksternalController::class, 'index'])->name('index');
        Route::get('/create', [PengajarEksternalController::class, 'create'])->name('create');
        Route::post('/', [PengajarEksternalController::class, 'store'])->name('store');
        Route::get('/{pengajarEksternal}', [PengajarEksternalController::class, 'show'])->name('show');
        Route::get('/{pengajarEksternal}/edit', [PengajarEksternalController::class, 'edit'])->name('edit');
        Route::put('/{pengajarEksternal}', [PengajarEksternalController::class, 'update'])->name('update');
        Route::delete('/{pengajarEksternal}', [PengajarEksternalController::class, 'destroy'])->name('destroy');
        Route::get('/{pengajarEksternal}/schedule', [PengajarEksternalController::class, 'schedule'])
         ->name('schedule');

    Route::get('/{pengajarEksternal}/attendance', [PengajarEksternalController::class, 'attendance'])
         ->name('attendance');
        // API untuk modal detail
        Route::get('/{pengajarEksternal}/detail', [PengajarEksternalController::class, 'getDetail'])
            ->name('get-detail');
        
        // Assign ke program & sub unit
        Route::post('/{pengajarEksternal}/assign-program', [PengajarEksternalController::class, 'assignProgram'])
            ->name('assign-program');
        
        Route::post('/{pengajarEksternal}/assign-sub-unit', [PengajarEksternalController::class, 'assignSubUnit'])
            ->name('assign-sub-unit');
    });

    // ========================================
    // PENGAJAR ASSIGNMENT ROUTES (INI YANG DIPERBAIKI)
    // ========================================
    
    // Assign ke program (dari halaman program jika ada)
    Route::post('programs/{program}/assign-pengajar', [PengajarAssignmentController::class, 'assignToProgram'])
        ->name('programs.assign-pengajar');

    // UPDATE assignment program → HANYA SATU INI SAJA
    Route::put('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'updateProgram'])
        ->name('pengajar-programs.update');

    // Hapus assignment program
    Route::delete('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'removeFromProgram'])
        ->name('pengajar-programs.destroy');

    // Assign ke sub unit
    Route::post('assign-pengajar-sub-unit', [PengajarAssignmentController::class, 'assignToSubUnit'])
        ->name('assign-pengajar-sub-unit');

    // UPDATE assignment sub unit
    Route::put('pengajar-sub-units/{assignment}', [PengajarAssignmentController::class, 'updateSubUnit'])
        ->name('pengajar-sub-units.update');

    // Hapus assignment sub unit
    Route::delete('pengajar-sub-units/{assignment}', [PengajarAssignmentController::class, 'removeFromSubUnit'])
        ->name('pengajar-sub-units.destroy');

    // ========================================
    // JENIS MATERI PELATIHAN (SUBMENU BARU)
    // ========================================
    Route::prefix('jenis-materi-pelatihan')->name('jenis-materi-pelatihan.')->group(function () {
        Route::get('/', [JenisMateriPelatihanController::class, 'index'])->name('index');
        Route::post('/', [JenisMateriPelatihanController::class, 'store'])->name('store');
        Route::put('/{jenisMateriPelatihan}', [JenisMateriPelatihanController::class, 'update'])->name('update');
        Route::delete('/{jenisMateriPelatihan}', [JenisMateriPelatihanController::class, 'destroy'])->name('destroy');
    });

    // ========================================
    // PENDIDIKAN (dipindah ke sini – level admin langsung)
    // ========================================
    Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
        Route::get('/', [PendidikanController::class, 'index'])->name('index');
        Route::post('/', [PendidikanController::class, 'store'])->name('store');
        Route::put('/{pendidikan}', [PendidikanController::class, 'update'])->name('update');
        Route::delete('/{pendidikan}', [PendidikanController::class, 'destroy'])->name('destroy');
    });
//    // ========================================
//     // ASSIGN PENGAJAR (KE PROGRAM & SUB UNIT)
//     // ========================================
//     Route::post('programs/{program}/assign-pengajar', [PengajarAssignmentController::class, 'assignToProgram'])
//         ->name('programs.assign-pengajar');
//     Route::post('assign-pengajar-sub-unit', [PengajarAssignmentController::class, 'assignToSubUnit'])
//         ->name('assign-pengajar-sub-unit');
//     Route::delete('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'removeFromProgram'])
//         ->name('pengajar-programs.destroy');
//     Route::delete('pengajar-sub-units/{assignment}', [PengajarAssignmentController::class, 'removeFromSubUnit'])
//         ->name('pengajar-sub-units.destroy');

    // // TAMBAHKAN BARIS INI DI SINI (di luar group pengajar-eksternal)
    // Route::put('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'updateAssignment'])
    //     ->name('pengajar-programs.update');
    // Schedule CRUD
    Route::get('/schedules/instructor/{instructor}/create', [ScheduleController::class, 'create'])->name('schedules.create');
    Route::post('/schedules/instructor/{instructor}', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    
    // Kehadiran
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/program/{program}', [AttendanceController::class, 'show'])->name('show');
        Route::post('/record', [AttendanceController::class, 'record'])->name('record');
        Route::get('/recap', [AttendanceController::class, 'recap'])->name('recap');
    });
    
    // Laporan
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
        Route::get('/active-programs', [ReportController::class, 'activePrograms'])->name('active-programs');
        Route::get('/active-participants', [ReportController::class, 'activeParticipants'])->name('active-participants');
        Route::get('/attendance-month', [ReportController::class, 'attendanceThisMonth'])->name('attendance-month');
        Route::get('/certificates', [ReportController::class, 'certificatesIssued'])->name('certificates');
    });
    
    // Sertifikat
    Route::prefix('certificates')->name('certificates.')->group(function () {
        Route::get('/', [CertificateController::class, 'index'])->name('index');
        Route::get('/create', [CertificateController::class, 'create'])->name('create');
        Route::post('/', [CertificateController::class, 'store'])->name('store');
        Route::get('/bulk-create', [CertificateController::class, 'bulkCreate'])->name('bulk-create');
        Route::post('/bulk-store', [CertificateController::class, 'bulkStore'])->name('bulk-store');
        Route::get('/{certificate}', [CertificateController::class, 'show'])->name('show');
        Route::get('/{certificate}/download', [CertificateController::class, 'download'])->name('download');
        Route::get('/{certificate}/preview', [CertificateController::class, 'preview'])->name('preview');
        Route::delete('/{certificate}', [CertificateController::class, 'destroy'])->name('destroy');
        Route::get('/certificate/verify/{certificate_number}', [CertificateController::class, 'verify'])->name('certificate.verify');
    });

    Route::prefix('independent-units')->name('independent-units.')->group(function () {
       Route::get('/', [IndependentUnitController::class, 'index'])->name('index');
        Route::get('/create', [IndependentUnitController::class, 'create'])->name('create');
        Route::post('/', [IndependentUnitController::class, 'store'])->name('store');
        Route::get('/{skkni}', [IndependentUnitController::class, 'show'])->name('show');
        Route::get('/{skkni}/edit', [IndependentUnitController::class, 'edit'])->name('edit');
        Route::put('/{skkni}', [IndependentUnitController::class, 'update'])->name('update');
        Route::delete('/{skkni}', [IndependentUnitController::class, 'destroy'])->name('destroy');

        Route::post('/store-skkni', [IndependentUnitController::class, 'storeSkkni'])->name('store-skkni');
        Route::match(['get', 'post'], '/sync-proglat', [IndependentUnitController::class, 'syncProglat'])
            ->name('sync-proglat');
            Route::put('/independent-units/{skkni}/file', [IndependentUnitController::class, 'updateFile'])->name('independent-units.file.update');
// ✅ TAMBAHKAN 2 BARIS INI
    Route::get('/{skkni}/preview-file', [IndependentUnitController::class, 'previewFile'])->name('preview-file');
    Route::get('/{skkni}/download-file', [IndependentUnitController::class, 'downloadFile'])->name('download-file');
            // CRUD SKKNI Manual
        Route::post('/store-skkni', [IndependentUnitController::class, 'storeSkkni'])->name('store-skkni');
        Route::put('/{skkni}/update-skkni', [IndependentUnitController::class, 'updateSkkni'])->name('update-skkni');
        Route::delete('/{skkni}/delete-skkni', [IndependentUnitController::class, 'destroySkkni'])->name('delete-skkni');
        
        // Show detail SKKNI dengan unit-unitnya (PENTING: Taruh setelah route CRUD SKKNI)
        Route::get('/{skkni}', [IndependentUnitController::class, 'show'])->name('show');
        
        // CRUD Unit Kompetensi di bawah SKKNI
        Route::post('/{skkni}/store-unit', [IndependentUnitController::class, 'storeUnit'])->name('store-unit');
        Route::put('/{unit}/update-unit', [IndependentUnitController::class, 'updateUnit'])->name('update-unit');
        Route::delete('/{unit}/delete-unit', [IndependentUnitController::class, 'destroyUnit'])->name('delete-unit');
    });

        // MANAJEMEN USER - PERBAIKAN FINAL
    Route::resource('users', UserController::class)->names([
        'index'   => 'users.index',
        'create'  => 'users.create',
        'store'   => 'users.store',
        'edit'    => 'users.edit',
        'update'  => 'users.update',
        'destroy' => 'users.destroy',
    ]);
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// ============================================
// INSTRUCTOR ROUTES
// ============================================
Route::middleware(['auth', 'role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');
    
    // Programs
    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\ProgramController::class, 'index'])->name('index');
        Route::get('/{program}', [\App\Http\Controllers\Instructor\ProgramController::class, 'show'])->name('show');
    });
    
    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\AttendanceController::class, 'index'])->name('index');
        Route::get('/{program}', [\App\Http\Controllers\Instructor\AttendanceController::class, 'show'])->name('show');
        Route::post('/record', [\App\Http\Controllers\Instructor\AttendanceController::class, 'record'])->name('record');
    });
    
    // My Attendance (BARU)
    Route::prefix('my-attendance')->name('my-attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'index'])
            ->name('index');
        Route::post('/clock-in', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'clockIn'])
            ->name('clock-in');
        Route::post('/clock-out', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'clockOut'])
            ->name('clock-out');
        Route::post('/leave', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'requestLeave'])
            ->name('leave');
        Route::get('/history', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'history'])
            ->name('history');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [\App\Http\Controllers\Instructor\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Instructor\ProfileController::class, 'updatePassword'])->name('password');
    });
});

// ============================================
// PARTICIPANT ROUTES
// ============================================
Route::middleware(['auth', 'role:participant'])->prefix('participant')->name('participant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Participant\DashboardController::class, 'index'])->name('dashboard');
    
    // Program
    Route::get('/program', [\App\Http\Controllers\Participant\ProgramController::class, 'show'])->name('program');
    
    // Attendance
    Route::get('/attendance', [\App\Http\Controllers\Participant\AttendanceController::class, 'index'])->name('attendance');
    
    // Certificate
    Route::prefix('certificate')->name('certificate.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Participant\CertificateController::class, 'index'])->name('index');
        Route::get('/{certificate}', [\App\Http\Controllers\Participant\CertificateController::class, 'preview'])->name('preview');
        Route::get('/{certificate}/download', [\App\Http\Controllers\Participant\CertificateController::class, 'download'])->name('download');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Participant\ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [\App\Http\Controllers\Participant\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Participant\ProfileController::class, 'updatePassword'])->name('password');
    });
});

require __DIR__.'/auth.php';
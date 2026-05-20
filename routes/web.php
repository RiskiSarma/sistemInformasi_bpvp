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
use App\Http\Controllers\PengajarEksternalScheduleController;
use App\Http\Controllers\PaketPengajarProgramController;
use App\Http\Controllers\PaketPengajarSubUnitController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/programs', [App\Http\Controllers\ProgramController::class, 'publicIndex'])
     ->name('programs.public');
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

    // Wilayah AJAX (tidak perlu auth karena dipakai di form)
    Route::middleware(['auth'])->prefix('api/wilayah')->name('wilayah.')->group(function () {
        Route::get('/cities',    [\App\Http\Controllers\WilayahController::class, 'cities'])->name('cities');
        Route::get('/districts', [\App\Http\Controllers\WilayahController::class, 'districts'])->name('districts');
        Route::get('/villages',  [\App\Http\Controllers\WilayahController::class, 'villages'])->name('villages');
    });
// ============================================
// ADMIN ROUTES
// ============================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ========================================
    // DAFTAR ULANG - VERIFIKASI DOKUMEN PESERTA
    // ========================================
    Route::prefix('daftar-ulang')->name('daftar-ulang.')->group(function () {
        Route::get('/', [\App\Http\Controllers\DaftarUlangController::class, 'index'])
            ->name('index');
        Route::post('/{document}/approve', [\App\Http\Controllers\DaftarUlangController::class, 'approve'])
            ->name('approve');
        Route::post('/{document}/reject', [\App\Http\Controllers\DaftarUlangController::class, 'reject'])
            ->name('reject');
        Route::get('/{document}/preview', [\App\Http\Controllers\DaftarUlangController::class, 'preview'])
            ->name('preview');
    });

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
                'index'   => 'units.index',
                'create'  => 'units.create',
                'store'   => 'units.store',
                'show'    => 'units.show',
                'edit'    => 'units.edit',
                'update'  => 'units.update',
                'destroy' => 'units.destroy',
            ]);

            Route::post('paket-units', [PaketUnitController::class, 'store'])->name('paket-units.store');
            Route::put('paket-units/{paketUnit}', [PaketUnitController::class, 'update'])->name('paket-units.update');
            Route::delete('paket-units/{paketUnit}', [PaketUnitController::class, 'destroy'])->name('paket-units.destroy');

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
        Route::get('/master/{masterProgram}/preview-file', [ProgramController::class, 'previewFile'])->name('master.preview-file');

        // Sync Kemnaker
        Route::get('/sync-kemnaker', [ProgramController::class, 'syncKemnaker'])->name('sync-kemnaker');
        Route::get('/sync-status', [ProgramController::class, 'syncStatus'])->name('sync-status');

        // CRUD Unit Kompetensi di Master Program
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

        Route::prefix('kejuruan-bidang')->name('kejuruan-bidang.')->group(function () {
            Route::get('/', [KejuruanBidangController::class, 'index'])->name('index');
            Route::post('/sync-kejuruan', [KejuruanBidangController::class, 'syncKejuruan'])->name('sync-kejuruan');
            Route::post('/', [KejuruanBidangController::class, 'storeKejuruan'])->name('kejuruan.store');
            Route::put('/kejuruan/{kejuruan}', [KejuruanBidangController::class, 'updateKejuruan'])->name('kejuruan.update');
            Route::delete('/kejuruan/{kejuruan}', [KejuruanBidangController::class, 'destroyKejuruan'])->name('kejuruan.destroy');
            Route::post('/bidang', [KejuruanBidangController::class, 'storeBidang'])->name('bidang.store');
            Route::put('/bidang/{bidang}', [KejuruanBidangController::class, 'updateBidang'])->name('bidang.update');
            Route::delete('/bidang/{bidang}', [KejuruanBidangController::class, 'destroyBidang'])->name('bidang.destroy');
        });

        Route::prefix('paket-pelatihan')->name('paket-pelatihan.')->group(function () {
            Route::get('/', [PaketPelatihanController::class, 'index'])->name('index');
            Route::post('/', [PaketPelatihanController::class, 'store'])->name('store');
            Route::put('/{paket}', [PaketPelatihanController::class, 'update'])->name('update');
            Route::delete('/{paket}', [PaketPelatihanController::class, 'destroy'])->name('destroy');

            Route::prefix('{paket}')->group(function () {
                Route::get('paket-units', [PaketUnitController::class, 'getUnits'])->name('paket-units.data');
                Route::post('paket-units', [PaketUnitController::class, 'store'])->name('paket-units.store');
                Route::put('paket-units/{paketUnit}', [PaketUnitController::class, 'update'])->name('paket-units.update');
                Route::delete('paket-units/{paketUnit}', [PaketUnitController::class, 'destroy'])->name('paket-units.destroy');

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
        Route::patch('/{program}/participants/{participant}/remove', [ProgramController::class, 'removeParticipant'])
            ->name('participants.remove');
        Route::get('/{program}/edit', [ProgramController::class, 'edit'])->name('edit');
        Route::put('/{program}', [ProgramController::class, 'update'])->name('update');
        Route::delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');

        // Dokumen Administrasi
        Route::prefix('/{program}/dokumen')->name('dokumen.')->group(function () {
            Route::get('/sk-peserta', [ProgramController::class, 'dokumenSkPeserta'])->name('sk-peserta');
            Route::get('/st-instruktur', [ProgramController::class, 'dokumenStInstruktur'])->name('st-instruktur');
            Route::get('/jadwal', [ProgramController::class, 'dokumenJadwal'])->name('jadwal');
            Route::get('/daftar-hadir', [ProgramController::class, 'dokumenDaftarHadir'])->name('daftar-hadir');
            Route::get('/biodata-peserta', [ProgramController::class, 'dokumenBiodataPeserta'])->name('biodata-peserta');
            Route::get('/sk-penyelenggara', [ProgramController::class, 'dokumenSkPenyelenggara'])->name('sk-penyelenggara');
            Route::get('/edit/{template}', [ProgramController::class, 'editTemplate'])->name('edit-template');
            Route::put('/update/{template}', [ProgramController::class, 'updateTemplate'])->name('update-template');
        });
    });

    // Peserta
    Route::get('participants/import', [ParticipantController::class, 'importForm'])
        ->name('participants.import.form');
    Route::post('participants/import', [ParticipantController::class, 'import'])
        ->name('participants.import');
    Route::get('participants/template', [ParticipantController::class, 'downloadTemplate'])
        ->name('participants.template');
    Route::resource('participants', ParticipantController::class)->names([
        'index'   => 'participants.index',
        'create'  => 'participants.create',
        'store'   => 'participants.store',
        'show'    => 'participants.show',
        'edit'    => 'participants.edit',
        'update'  => 'participants.update',
        'destroy' => 'participants.destroy',
    ]);

    // Instruktur
    Route::resource('instructors', InstructorController::class);
    Route::get('/instructors/{instructor}/schedule', [InstructorController::class, 'schedule'])
        ->name('instructors.schedule');
    Route::get('/instructors/{instructor}/attendance', [InstructorAttendanceController::class, 'show'])
        ->name('instructors.attendance');
    Route::post('/instructors/{instructor}/attendance', [InstructorAttendanceController::class, 'store'])
        ->name('instructors.attendance.store');
    Route::put('/instructors/attendance/{attendance}', [InstructorAttendanceController::class, 'update'])
        ->name('instructors.attendance.update');
    Route::delete('/instructors/attendance/{attendance}', [InstructorAttendanceController::class, 'destroy'])
        ->name('instructors.attendance.destroy');

    // Pengajar Eksternal
    Route::prefix('pengajar-eksternal')->name('pengajar-eksternal.')->group(function () {
        Route::get('/', [PengajarEksternalController::class, 'index'])->name('index');
        Route::get('/create', [PengajarEksternalController::class, 'create'])->name('create');
        Route::post('/', [PengajarEksternalController::class, 'store'])->name('store');
        Route::get('/{pengajarEksternal}', [PengajarEksternalController::class, 'show'])->name('show');
        Route::get('/{pengajarEksternal}/edit', [PengajarEksternalController::class, 'edit'])->name('edit');
        Route::put('/{pengajarEksternal}', [PengajarEksternalController::class, 'update'])->name('update');
        Route::delete('/{pengajarEksternal}', [PengajarEksternalController::class, 'destroy'])->name('destroy');
        Route::get('/{pengajarEksternal}/schedule', [PengajarEksternalController::class, 'schedule'])->name('schedule');
        Route::get('{pengajarEksternal}/schedules/create', [PengajarEksternalScheduleController::class, 'create'])->name('schedules.create');
        Route::post('{pengajarEksternal}/schedules', [PengajarEksternalScheduleController::class, 'store'])->name('schedules.store');
        Route::get('schedules/{schedule}/edit', [PengajarEksternalScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{schedule}', [PengajarEksternalScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('schedules/{schedule}', [PengajarEksternalScheduleController::class, 'destroy'])->name('schedules.destroy');
        Route::get('/{pengajarEksternal}/attendance', [PengajarEksternalController::class, 'attendance'])->name('attendance');
        Route::get('/{pengajarEksternal}/detail', [PengajarEksternalController::class, 'getDetail'])->name('get-detail');
        Route::post('/{pengajarEksternal}/assign-program', [PengajarEksternalController::class, 'assignProgram'])->name('assign-program');
        Route::post('/{pengajarEksternal}/assign-sub-unit', [PengajarEksternalController::class, 'assignSubUnit'])->name('assign-sub-unit');
    });

    // Pengajar Assignment
    Route::post('programs/{program}/assign-pengajar', [PengajarAssignmentController::class, 'assignToProgram'])
        ->name('programs.assign-pengajar');
    Route::put('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'updateProgram'])
        ->name('pengajar-programs.update');
    Route::delete('pengajar-programs/{assignment}', [PengajarAssignmentController::class, 'removeFromProgram'])
        ->name('pengajar-programs.destroy');
    Route::post('assign-pengajar-sub-unit', [PengajarAssignmentController::class, 'assignToSubUnit'])
        ->name('assign-pengajar-sub-unit');
    Route::put('pengajar-sub-units/{assignment}', [PengajarAssignmentController::class, 'updateSubUnit'])
        ->name('pengajar-sub-units.update');
    Route::delete('pengajar-sub-units/{assignment}', [PengajarAssignmentController::class, 'removeFromSubUnit'])
        ->name('pengajar-sub-units.destroy');

    // Jenis Materi Pelatihan
    Route::prefix('jenis-materi-pelatihan')->name('jenis-materi-pelatihan.')->group(function () {
        Route::get('/', [JenisMateriPelatihanController::class, 'index'])->name('index');
        Route::post('/', [JenisMateriPelatihanController::class, 'store'])->name('store');
        Route::put('/{jenisMateriPelatihan}', [JenisMateriPelatihanController::class, 'update'])->name('update');
        Route::delete('/{jenisMateriPelatihan}', [JenisMateriPelatihanController::class, 'destroy'])->name('destroy');
    });

    // Pendidikan
    Route::prefix('pendidikan')->name('pendidikan.')->group(function () {
        Route::get('/', [PendidikanController::class, 'index'])->name('index');
        Route::post('/', [PendidikanController::class, 'store'])->name('store');
        Route::put('/{pendidikan}', [PendidikanController::class, 'update'])->name('update');
        Route::delete('/{pendidikan}', [PendidikanController::class, 'destroy'])->name('destroy');
    });

    // Schedule
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

    // Independent Units (SKKNI)
    Route::prefix('independent-units')->name('independent-units.')->group(function () {
        Route::get('/', [IndependentUnitController::class, 'index'])->name('index');
        Route::get('/create', [IndependentUnitController::class, 'create'])->name('create');
        Route::post('/', [IndependentUnitController::class, 'store'])->name('store');
        Route::get('/{skkni}/edit', [IndependentUnitController::class, 'edit'])->name('edit');
        Route::put('/{skkni}', [IndependentUnitController::class, 'update'])->name('update');
        Route::delete('/{skkni}', [IndependentUnitController::class, 'destroy'])->name('destroy');
        Route::post('/store-skkni', [IndependentUnitController::class, 'storeSkkni'])->name('store-skkni');
        Route::match(['get', 'post'], '/sync-proglat', [IndependentUnitController::class, 'syncProglat'])->name('sync-proglat');
        Route::get('/{skkni}/preview-file', [IndependentUnitController::class, 'previewFile'])->name('preview-file');
        Route::get('/{skkni}/download-file', [IndependentUnitController::class, 'downloadFile'])->name('download-file');
        Route::put('/{skkni}/update-skkni', [IndependentUnitController::class, 'updateSkkni'])->name('update-skkni');
        Route::delete('/{skkni}/delete-skkni', [IndependentUnitController::class, 'destroySkkni'])->name('delete-skkni');
        Route::get('/{skkni}', [IndependentUnitController::class, 'show'])->name('show');
        Route::post('/{skkni}/store-unit', [IndependentUnitController::class, 'storeUnit'])->name('store-unit');
        Route::put('/{unit}/update-unit', [IndependentUnitController::class, 'updateUnit'])->name('update-unit');
        Route::delete('/{unit}/delete-unit', [IndependentUnitController::class, 'destroyUnit'])->name('delete-unit');
    });

    // Manajemen User
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

    // Profile Admin
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

    Route::get('/dashboard', [\App\Http\Controllers\Instructor\DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('programs')->name('programs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\ProgramController::class, 'index'])->name('index');
        Route::get('/{program}', [\App\Http\Controllers\Instructor\ProgramController::class, 'show'])->name('show');
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\AttendanceController::class, 'index'])->name('index');
        Route::get('/{program}', [\App\Http\Controllers\Instructor\AttendanceController::class, 'show'])->name('show');
        Route::post('/record', [\App\Http\Controllers\Instructor\AttendanceController::class, 'record'])->name('record');
    });

    Route::prefix('my-attendance')->name('my-attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'index'])->name('index');
        Route::post('/clock-in', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'clockIn'])->name('clock-in');
        Route::post('/clock-out', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'clockOut'])->name('clock-out');
        Route::post('/leave', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'requestLeave'])->name('leave');
        Route::get('/history', [\App\Http\Controllers\Instructor\MyAttendanceController::class, 'history'])->name('history');
    });

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

    Route::get('/dashboard', [\App\Http\Controllers\Participant\DashboardController::class, 'index'])->name('dashboard');

    // Participant routes - ubah bagian program
Route::get('/program', [\App\Http\Controllers\Participant\ProgramController::class, 'index'])->name('program');
Route::get('/program/{participant}', [\App\Http\Controllers\Participant\ProgramController::class, 'show'])->name('program.show');

    Route::get('/attendance', [\App\Http\Controllers\Participant\AttendanceController::class, 'index'])->name('attendance');

    // ========================================
    // DAFTAR ULANG - UPLOAD BERKAS PESERTA
    // ========================================
    Route::prefix('daftar-ulang')->name('daftar-ulang.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Participant\DaftarUlangController::class, 'index'])
            ->name('index');
        Route::post('/upload', [\App\Http\Controllers\Participant\DaftarUlangController::class, 'upload'])
            ->name('upload');
        Route::get('/{document}/preview', [\App\Http\Controllers\Participant\DaftarUlangController::class, 'preview'])
            ->name('preview');
        Route::delete('/{document}', [\App\Http\Controllers\Participant\DaftarUlangController::class, 'destroy'])
            ->name('destroy');
    });

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
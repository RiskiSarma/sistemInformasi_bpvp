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
use App\Http\Controllers\CertificateController;
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
        
        // Master Program
        Route::get('/master', [ProgramController::class, 'master'])->name('master');
        Route::post('/master', [ProgramController::class, 'storeMaster'])->name('master.store');
        Route::get('/master/{masterProgram}', [ProgramController::class, 'showMaster'])->name('master.show');
        Route::get('/master/{masterProgram}/edit', [ProgramController::class, 'editMaster'])->name('master.edit');
        Route::put('/master/{masterProgram}', [ProgramController::class, 'updateMaster'])->name('master.update');
        Route::delete('/master/{masterProgram}', [ProgramController::class, 'destroyMaster'])->name('master.destroy');
        
        // Unit Kompetensi
        Route::get('/units', [ProgramController::class, 'units'])->name('units');
        Route::post('/units', [ProgramController::class, 'storeUnit'])->name('units.store');
        Route::get('/units/{unit}', [ProgramController::class, 'showUnit'])->name('units.show');
        Route::get('/units/{unit}/edit', [ProgramController::class, 'editUnit'])->name('units.edit');
        Route::put('/units/{unit}', [ProgramController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit}', [ProgramController::class, 'destroyUnit'])->name('units.destroy');
        Route::get('/{program}', [ProgramController::class, 'show'])->name('show');
    });
    
    // Peserta
    Route::resource('participants', ParticipantController::class);
    
    // Instruktur
    Route::resource('instructors', InstructorController::class);
    Route::get('/instructors/{instructor}/schedule', [InstructorController::class, 'schedule'])->name('instructors.schedule');
    
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
    });
    
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
        Route::get('/download', [\App\Http\Controllers\Participant\CertificateController::class, 'download'])->name('download');
    });
    
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Participant\ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [\App\Http\Controllers\Participant\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Participant\ProfileController::class, 'updatePassword'])->name('password');
    });
});

require __DIR__.'/auth.php';
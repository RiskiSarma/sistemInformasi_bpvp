<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorAttendance;
use App\Models\Schedule;
use App\Models\Instructor;
use App\Models\PengajarEksternal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyAttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        if (!$internal && !$external) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }

        $today = now();

        // ==================== JADWAL HARI INI ====================
        if ($internal) {
            $schedules = $internal->schedules()
                ->with([
                    'program.masterProgram',
                    'attendance' => function ($query) use ($today) {
                        $query->whereDate('date', $today->format('Y-m-d'));
                    }
                ])
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
        } else {
            $schedules = Schedule::where('pengajar_eksternal_id', $external->id)
                ->with([
                    'program.masterProgram',
                    'attendance' => function ($query) use ($today) {
                        $query->whereDate('date', $today->format('Y-m-d'));
                    }
                ])
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get();
        }

        // ==================== RIWAYAT KEHADIRAN ====================
        $attendancesQuery = InstructorAttendance::with('schedule.program.masterProgram')
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year);

        if ($internal) {
            $attendancesQuery->where('instructor_id', $internal->id)
                             ->where('instructor_type', 'internal');
        } else {
            $attendancesQuery->where('pengajar_eksternal_id', $external->id)
                             ->where('instructor_type', 'external');
        }

        $attendances = $attendancesQuery->orderBy('date', 'desc')->get();

        $stats = [
            'present'     => $attendances->where('status', 'present')->count(),
            'late'        => $attendances->where('status', 'late')->count(),
            'sick'        => $attendances->where('status', 'sick')->count(),
            'permission'  => $attendances->whereIn('status', ['permission', 'excused'])->count(),
            'absent'      => $attendances->where('status', 'absent')->count(),
            'total_hours' => $attendances->sum('duration') / 60,
        ];

        return view('instructor-area.my-attendance.index', compact(
            'internal',
            'external',
            'schedules', 
            'attendances', 
            'stats', 
            'today'
        ));
    }

    // ==================== CLOCK IN ====================
    public function clockIn(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        if (!$internal && !$external) {
            return redirect()->back()->with('error', 'Data instruktur tidak ditemukan');
        }

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Cek existing attendance
        $existingQuery = InstructorAttendance::where('schedule_id', $schedule->id)
            ->where('date', now()->format('Y-m-d'));

        if ($internal) {
            $existingQuery->where('instructor_id', $internal->id)
                          ->where('instructor_type', 'internal');
        } else {
            $existingQuery->where('pengajar_eksternal_id', $external->id)
                          ->where('instructor_type', 'external');
        }

        $existing = $existingQuery->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah Absen Masuk untuk jadwal ini hari ini');
        }

        $scheduledTime = Carbon::parse(now()->format('Y-m-d') . ' ' . $schedule->start_time);
        $now = now();
        $status = ($now->diffInMinutes($scheduledTime, false) < -15) ? 'late' : 'present';

        $location = null;
        if ($request->latitude && $request->longitude) {
            $location = $request->latitude . ',' . $request->longitude;
        }

        // ✅ PERBAIKAN: Simpan sesuai tipe instructor
        $data = [
            'schedule_id'   => $schedule->id,
            'date'          => now()->format('Y-m-d'),
            'check_in'      => $now->format('H:i:s'),
            'status'        => $status,
            'location'      => $location,
        ];

        if ($internal) {
            $data['instructor_id'] = $internal->id;
            $data['instructor_type'] = 'internal';
        } else {
            $data['pengajar_eksternal_id'] = $external->id;
            $data['instructor_type'] = 'external';
        }

        InstructorAttendance::create($data);

        $statusLabel = ($status === 'late') ? 'Terlambat' : 'Tepat waktu';
        return redirect()->back()->with('success', "Absen Masuk berhasil! Status: {$statusLabel}");
    }

    // ==================== CLOCK OUT ====================
    public function clockOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:instructor_attendances,id',
            'notes'         => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        if (!$internal && !$external) {
            return redirect()->back()->with('error', 'Data instruktur tidak ditemukan');
        }

        $attendance = InstructorAttendance::findOrFail($request->attendance_id);

        // ✅ Verifikasi kepemilikan
        if ($internal && ($attendance->instructor_id !== $internal->id || $attendance->instructor_type !== 'internal')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        if ($external && ($attendance->pengajar_eksternal_id !== $external->id || $attendance->instructor_type !== 'external')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Anda sudah Absen Keluar untuk jadwal ini');
        }

        $dateStr = ($attendance->date instanceof Carbon)
                    ? $attendance->date->toDateString()
                    : Carbon::parse($attendance->date)->toDateString();

        $checkIn = Carbon::parse($dateStr . ' ' . $attendance->check_in);
        $checkOut = now();
        $durationMinutes = (int) $checkIn->diffInMinutes($checkOut);

        $attendance->update([
            'check_out' => $checkOut->format('H:i:s'),
            'notes'     => $request->notes,
            'duration'  => $durationMinutes,
        ]);

        $hours = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;

        return redirect()->back()->with('success', "Absen Keluar berhasil! Durasi: {$hours} jam {$minutes} menit");
    }

    // ==================== REQUEST LEAVE ====================
    public function requestLeave(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'leave_type'  => 'required|in:sick,excused',
            'notes'       => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        if (!$internal && !$external) {
            return redirect()->back()->with('error', 'Data instruktur tidak ditemukan');
        }

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Cek existing
        $existingQuery = InstructorAttendance::where('schedule_id', $schedule->id)
            ->where('date', now()->format('Y-m-d'));

        if ($internal) {
            $existingQuery->where('instructor_id', $internal->id)
                          ->where('instructor_type', 'internal');
        } else {
            $existingQuery->where('pengajar_eksternal_id', $external->id)
                          ->where('instructor_type', 'external');
        }

        $existing = $existingQuery->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Sudah ada data kehadiran untuk jadwal ini hari ini');
        }

        // ✅ Simpan sesuai tipe
        $data = [
            'schedule_id'   => $schedule->id,
            'date'          => now()->format('Y-m-d'),
            'status'        => $request->leave_type,
            'notes'         => $request->notes,
        ];

        if ($internal) {
            $data['instructor_id'] = $internal->id;
            $data['instructor_type'] = 'internal';
        } else {
            $data['pengajar_eksternal_id'] = $external->id;
            $data['instructor_type'] = 'external';
        }

        InstructorAttendance::create($data);

        $label = ($request->leave_type === 'sick') ? 'Sakit' : 'Izin';
        return redirect()->back()->with('success', "Permohonan {$label} berhasil diajukan");
    }

    // ==================== HISTORY ====================
    public function history(Request $request)
    {
        $user = Auth::user();
        
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        if (!$internal && !$external) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }

        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

        // Query attendance
        $allAttendancesQuery = InstructorAttendance::whereBetween('date', [$startDate, $endDate]);

        if ($internal) {
            $allAttendancesQuery->where('instructor_id', $internal->id)
                                ->where('instructor_type', 'internal');
        } else {
            $allAttendancesQuery->where('pengajar_eksternal_id', $external->id)
                                ->where('instructor_type', 'external');
        }

        $allAttendances = $allAttendancesQuery->get();

        $stats = [
            'total_present'    => $allAttendances->where('status', 'present')->count(),
            'total_late'       => $allAttendances->where('status', 'late')->count(),
            'total_absent'     => $allAttendances->where('status', 'absent')->count(),
            'total_permission' => $allAttendances->whereIn('status', ['permission', 'excused'])->count(),
            'total_sick'       => $allAttendances->where('status', 'sick')->count(),
        ];

        $attendancesQuery = InstructorAttendance::with(['schedule.program.masterProgram'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($internal) {
            $attendancesQuery->where('instructor_id', $internal->id)
                             ->where('instructor_type', 'internal');
        } else {
            $attendancesQuery->where('pengajar_eksternal_id', $external->id)
                             ->where('instructor_type', 'external');
        }

        $attendances = $attendancesQuery->orderBy('date', 'desc')->paginate(30);

        return view('instructor-area.my-attendance.history', compact(
            'attendances', 'startDate', 'endDate', 'month', 'stats'
        ));
    }
}
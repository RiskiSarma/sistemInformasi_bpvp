<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorAttendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyAttendanceController extends Controller
{
    public function index()
    {
        $instructor = Auth::user()->instructor;

        if (!$instructor) {
            return redirect()->route('instructor.dashboard')->with('error', 'Data instruktur tidak ditemukan');
        }

        $today = now();

        $schedules = $instructor->schedules()
            ->with([
                'program.masterProgram',
                'attendance' => function ($query) {
                    $query->where('date', now()->format('Y-m-d'));
                }
            ])
            ->active()
            ->ordered()
            ->get();

        $attendances = InstructorAttendance::with('schedule.program.masterProgram')
            ->where('instructor_id', $instructor->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'present'     => $attendances->where('status', 'present')->count(),
            'late'        => $attendances->where('status', 'late')->count(),
            'sick'        => $attendances->where('status', 'sick')->count(),
            'permission'  => $attendances->whereIn('status', ['permission', 'excused'])->count(),
            'absent'      => $attendances->where('status', 'absent')->count(),
            'total_hours' => $attendances->sum('duration') / 60,
        ];

        return view('instructor-area.my-attendance.index', compact('instructor', 'schedules', 'attendances', 'stats', 'today'));
    }

    public function clockIn(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
        ]);

        $instructor = Auth::user()->instructor;
        $schedule   = Schedule::findOrFail($request->schedule_id);

        $existing = InstructorAttendance::where('instructor_id', $instructor->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah Absen Masuk untuk jadwal ini hari ini');
        }

        $scheduledTime = Carbon::parse(now()->format('Y-m-d') . ' ' . $schedule->start_time);
        $now           = now();
        $status        = ($now->diffInMinutes($scheduledTime, false) < -15) ? 'late' : 'present';

        $location = null;
        if ($request->latitude && $request->longitude) {
            $location = $request->latitude . ',' . $request->longitude;
        }

        InstructorAttendance::create([
            'instructor_id' => $instructor->id,
            'schedule_id'   => $schedule->id,
            'date'          => now()->format('Y-m-d'),
            'check_in'      => $now->format('H:i:s'),
            'status'        => $status,
            'location'      => $location,
        ]);

        $statusLabel = ($status === 'late') ? 'Terlambat' : 'Tepat waktu';
        return redirect()->back()->with('success', "Absen Masuk berhasil! Status: {$statusLabel}");
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:instructor_attendances,id',
            'notes'         => 'nullable|string|max:500',
        ]);

        $instructor = Auth::user()->instructor;
        $attendance = InstructorAttendance::findOrFail($request->attendance_id);

        if ($attendance->instructor_id !== $instructor->id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Anda sudah Absen Keluar untuk jadwal ini');
        }

        // FIX "Double time specification":
        // $attendance->date bisa jadi Carbon object karena cast 'date' di model
        // Gunakan toDateString() untuk dapat "Y-m-d" murni tanpa "00:00:00"
        $dateStr         = ($attendance->date instanceof \Carbon\Carbon)
                            ? $attendance->date->toDateString()
                            : Carbon::parse($attendance->date)->toDateString();
        $checkIn         = Carbon::parse($dateStr . ' ' . $attendance->check_in);
        $checkOut        = now();
        $durationMinutes = (int) $checkIn->diffInMinutes($checkOut);

        $attendance->update([
            'check_out' => $checkOut->format('H:i:s'),
            'notes'     => $request->notes,
            'duration'  => $durationMinutes,
        ]);

        $hours   = floor($durationMinutes / 60);
        $minutes = $durationMinutes % 60;
        return redirect()->back()->with('success', "Absen Keluar berhasil! Durasi: {$hours} jam {$minutes} menit");
    }

    public function requestLeave(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'leave_type'  => 'required|in:sick,excused',
            'notes'       => 'required|string|max:500',
        ]);

        $instructor = Auth::user()->instructor;
        $schedule   = Schedule::findOrFail($request->schedule_id);

        $existing = InstructorAttendance::where('instructor_id', $instructor->id)
            ->where('schedule_id', $schedule->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Sudah ada data kehadiran untuk jadwal ini hari ini');
        }

        // FIX "Data truncated for column status":
        // Gunakan langsung nilai 'excused' atau 'sick' — keduanya ada di ENUM DB
        // Jangan map ke 'permission' karena nilai itu tidak ada di ENUM
        InstructorAttendance::create([
            'instructor_id' => $instructor->id,
            'schedule_id'   => $schedule->id,
            'date'          => now()->format('Y-m-d'),
            'status'        => $request->leave_type, // 'excused' atau 'sick' langsung
            'notes'         => $request->notes,
        ]);

        $label = ($request->leave_type === 'sick') ? 'Sakit' : 'Izin';
        return redirect()->back()->with('success', "Permohonan {$label} berhasil diajukan");
    }

    public function history(Request $request)
    {
        $instructor = Auth::user()->instructor;

        if (!$instructor) {
            return redirect()->route('instructor.dashboard')->with('error', 'Data instruktur tidak ditemukan');
        }

        $month     = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
        $endDate   = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');

        $allAttendances = InstructorAttendance::where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $stats = [
            'total_present'    => $allAttendances->where('status', 'present')->count(),
            'total_late'       => $allAttendances->where('status', 'late')->count(),
            'total_absent'     => $allAttendances->where('status', 'absent')->count(),
            'total_permission' => $allAttendances->whereIn('status', ['permission', 'excused'])->count(),
            'total_sick'       => $allAttendances->where('status', 'sick')->count(),
        ];

        $attendances = InstructorAttendance::with(['schedule.program.masterProgram'])
            ->where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(30);

        return view('instructor-area.my-attendance.history', compact('attendances', 'startDate', 'endDate', 'month', 'stats'));
    }
}
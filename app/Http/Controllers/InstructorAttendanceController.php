<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\InstructorAttendance;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAttendanceController extends Controller
{
    public function show(Instructor $instructor)
    {
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = request('end_date', now()->format('Y-m-d'));

        // FIX: eager load schedule->program->masterProgram (bukan program langsung)
        $attendances = InstructorAttendance::with(['schedule.program.masterProgram'])
            ->where('instructor_id', $instructor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(20);

        // FIX: hitung excused juga (nama baru untuk 'permission' di DB)
        $monthQuery = InstructorAttendance::where('instructor_id', $instructor->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $stats = [
            'present'    => (clone $monthQuery)->where('status', 'present')->count(),
            'late'       => (clone $monthQuery)->where('status', 'late')->count(),
            'absent'     => (clone $monthQuery)->where('status', 'absent')->count(),
            'sick'       => (clone $monthQuery)->where('status', 'sick')->count(),
            'excused'    => (clone $monthQuery)->where('status', 'excused')->count(),
            'permission' => (clone $monthQuery)->where('status', 'permission')->count(),
        ];

        return view('instructors.attendance', compact('instructor', 'attendances', 'stats'));
    }

    public function store(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'date'        => 'required|date',
            'check_in'    => 'nullable|date_format:H:i',
            'check_out'   => 'nullable|date_format:H:i',
            'status'      => 'required|in:present,late,absent,sick,excused',
            'notes'       => 'nullable|string',
        ]);

        InstructorAttendance::create([
            'instructor_id' => $instructor->id,
            'schedule_id'   => $validated['schedule_id'],
            'date'          => $validated['date'],
            'check_in'      => $validated['check_in']  ?? null,
            'check_out'     => $validated['check_out'] ?? null,
            'status'        => $validated['status'],
            'notes'         => $validated['notes']     ?? null,
            'created_by'    => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Data absensi berhasil ditambahkan');
    }

    public function update(Request $request, InstructorAttendance $attendance)
    {
        $validated = $request->validate([
            'check_in'  => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status'    => 'required|in:present,late,absent,sick,excused',
            'notes'     => 'nullable|string',
        ]);

        // Hitung ulang durasi jika check_in & check_out diisi
        if (!empty($validated['check_in']) && !empty($validated['check_out'])) {
            $dateStr = ($attendance->date instanceof \Carbon\Carbon)
                ? $attendance->date->toDateString()
                : \Carbon\Carbon::parse($attendance->date)->toDateString();

            $in  = \Carbon\Carbon::parse($dateStr . ' ' . $validated['check_in']);
            $out = \Carbon\Carbon::parse($dateStr . ' ' . $validated['check_out']);
            $validated['duration'] = (int) $in->diffInMinutes($out);
        }

        $attendance->update($validated);

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui');
    }

    public function destroy(InstructorAttendance $attendance)
    {
        $attendance->delete();
        return redirect()->back()->with('success', 'Data absensi berhasil dihapus');
    }
}
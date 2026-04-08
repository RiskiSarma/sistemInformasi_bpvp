<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorAttendance;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InstructorSelfAttendanceController extends Controller
{
    /**
     * Display instructor's own attendance
     */
    public function index(Request $request)
    {
        $instructor = Auth::user()->instructor;

        // Get active programs for this instructor
        $programs = Program::whereHas('programInstructors', function($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })
        ->with(['masterProgram', 'paketPelatihan'])
        ->where('end_date', '>=', now())
        ->orderBy('start_date', 'desc')
        ->get();

        // Selected program
        $selectedProgramId = $request->input('program_id');
        $selectedProgram = $selectedProgramId ? Program::find($selectedProgramId) : $programs->first();

        // Date range
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Get attendance records
        $attendances = collect([]);
        if ($selectedProgram) {
            $attendances = InstructorAttendance::byProgram($selectedProgram->id)
                ->byInstructor($instructor->id)
                ->byDateRange($startDate, $endDate)
                ->orderBy('date', 'desc')
                ->get();
        }

        // Statistics
        $stats = [
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'izin' => $attendances->where('status', 'izin')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'alpha' => $attendances->where('status', 'alpha')->count(),
            'total' => $attendances->count(),
        ];

        return view('instructor.self-attendance.index', compact(
            'programs',
            'selectedProgram',
            'attendances',
            'stats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Check-in
     */
    public function checkIn(Request $request)
    {
        $instructor = Auth::user()->instructor;
        $programId = $request->input('program_id');
        $date = now()->format('Y-m-d');

        // Validasi program
        $program = Program::whereHas('programInstructors', function($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })->findOrFail($programId);

        // Check if already checked in today
        $existing = InstructorAttendance::where([
            'program_id' => $programId,
            'instructor_id' => $instructor->id,
            'date' => $date,
        ])->first();

        if ($existing && $existing->check_in) {
            return redirect()->back()->with('error', 'Anda sudah check-in hari ini!');
        }

        InstructorAttendance::updateOrCreate(
            [
                'program_id' => $programId,
                'instructor_id' => $instructor->id,
                'date' => $date,
            ],
            [
                'check_in' => now()->format('H:i'),
                'status' => 'hadir',
                'created_by' => 'self',
            ]
        );

        return redirect()->back()->with('success', 'Check-in berhasil! Jam: ' . now()->format('H:i'));
    }

    /**
     * Check-out
     */
    public function checkOut(Request $request)
    {
        $instructor = Auth::user()->instructor;
        $programId = $request->input('program_id');
        $date = now()->format('Y-m-d');

        $attendance = InstructorAttendance::where([
            'program_id' => $programId,
            'instructor_id' => $instructor->id,
            'date' => $date,
        ])->firstOrFail();

        if (!$attendance->check_in) {
            return redirect()->back()->with('error', 'Anda belum check-in!');
        }

        if ($attendance->check_out) {
            return redirect()->back()->with('error', 'Anda sudah check-out hari ini!');
        }

        $attendance->update([
            'check_out' => now()->format('H:i'),
        ]);

        return redirect()->back()->with('success', 'Check-out berhasil! Jam: ' . now()->format('H:i'));
    }

    /**
     * Request izin/sakit
     */
    public function requestLeave(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'date' => 'required|date',
            'status' => 'required|in:izin,sakit',
            'notes' => 'required|string|max:500',
        ]);

        $instructor = Auth::user()->instructor;

        // Validasi program
        $program = Program::whereHas('programInstructors', function($q) use ($instructor) {
            $q->where('instructor_id', $instructor->id);
        })->findOrFail($validated['program_id']);

        InstructorAttendance::updateOrCreate(
            [
                'program_id' => $validated['program_id'],
                'instructor_id' => $instructor->id,
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'created_by' => 'self',
            ]
        );

        return redirect()->back()->with('success', 'Pengajuan ' . $validated['status'] . ' berhasil diajukan!');
    }
}
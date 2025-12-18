<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Attendance;
use App\Models\Instructor;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cari instructor dari tabel instructors
        $instructor = Instructor::where('user_id', $user->id)->first();
        
        if (!$instructor) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }
        
        // Get ongoing programs dari tabel programs
        $programs = Program::where('instructor_id', $instructor->id)
            ->where('status', 'ongoing')
            ->with(['participants'])
            ->get();
        
        return view('instructor-area.attendance.index', compact('programs'));
    }
    
    public function show(Program $program)
    {
        $user = auth()->user();
        
        // Cari instructor dari tabel instructors
        $instructor = Instructor::where('user_id', $user->id)->first();
        
        // Pastikan program ini diajar oleh instructor yang login
        if (!$instructor || $program->instructor_id !== $instructor->id) {
            abort(403, 'Anda tidak memiliki akses ke program ini');
        }
        
        // Get participants with today's attendance dari tabel participants
        $participants = Participant::where('program_id', $program->id)
            ->with(['user', 'attendances' => function($query) {
                $query->whereDate('date', today());
            }])
            ->where('status', 'active')
            ->get();
        
        $date = request('date', today()->format('Y-m-d'));
        
        // Get attendance for specific date
        $today = Carbon::parse($date);
        $attendances = Attendance::where('program_id', $program->id)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('participant_id');
        
        return view('instructor-area.attendance.show', compact('program', 'participants', 'date', 'attendances', 'today'));
    }
    
    public function record(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.participant_id' => 'required|exists:participants,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string',
        ]);

        $user = auth()->user();
        
        // Cari instructor
        $instructor = Instructor::where('user_id', $user->id)->first();
        
        if (!$instructor) {
            return redirect()->back()->with('error', 'Data instruktur tidak ditemukan');
        }
        
        // Verify program belongs to instructor
        $program = Program::findOrFail($request->program_id);
        if ($program->instructor_id !== $instructor->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            foreach ($request->attendances as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'program_id' => $request->program_id,
                        'participant_id' => $attendanceData['participant_id'],
                        'date' => $request->date,
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'notes' => $attendanceData['notes'] ?? null,
                        'recorded_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Kehadiran berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mencatat kehadiran: ' . $e->getMessage());
        }
    }
}
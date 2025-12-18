<?php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Program;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::with(['masterProgram', 'participants']);
        
        // Filter berdasarkan status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan ongoing dan completed
            $query->whereIn('status', ['ongoing', 'completed']);
        }
        
        $programs = $query->orderBy('start_date', 'desc')->get();
        
        return view('attendance.index', compact('programs'));
    }

    public function show(Program $program)
    {
        $program->load(['masterProgram', 'participants']);

        // Get all attendance records for this program
        $attendanceRecords = Attendance::where('program_id', $program->id)
            ->with('participant')
            ->orderBy('date', 'desc')
            ->get();
        
        // Get unique dates
        $dates = $attendanceRecords->pluck('date')
            ->unique()
            ->sort()
            ->reverse()
            ->take(10)
            ->values(); // Reset keys untuk memastikan array sequential

        // Group by date - PERBAIKAN: Convert date ke string untuk key
        $attendances = $attendanceRecords->groupBy(function($item) {
            return $item->date instanceof Carbon 
                ? $item->date->format('Y-m-d') 
                : $item->date;
        });

        return view('attendance.show', compact('program', 'dates', 'attendances'));
    }

    public function record(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.participant_id' => 'required|exists:participants,id',
            'attendances.*.status' => 'required|in:present,absent,excused,late',
            'attendances.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Loop through each attendance
            foreach ($validated['attendances'] as $attendanceData) {
                // Pastikan participant_id tidak kosong
                if (empty($attendanceData['participant_id'])) {
                    continue;
                }

                Attendance::updateOrCreate(
                    [
                        'program_id' => $validated['program_id'],
                        'participant_id' => $attendanceData['participant_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $attendanceData['status'],
                        'notes' => $attendanceData['notes'] ?? null,
                    ]
                );
            }
            
            DB::commit();
            return redirect()->back()->with('success', 'Kehadiran berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mencatat kehadiran: ' . $e->getMessage());
        }
    }

    public function recap(Request $request)
    {
        $query = Program::with(['masterProgram', 'participants'])
            ->whereIn('status', ['ongoing', 'completed']);

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $programs = $query->orderBy('start_date', 'desc')->get();

        // Calculate attendance statistics for each program
        foreach ($programs as $program) {
            $program->attendance_stats = $program->participants->map(function($participant) use ($program) {
                $attendances = Attendance::where('program_id', $program->id)
                    ->where('participant_id', $participant->id)
                    ->get();
                
                $presentCount = $attendances->where('status', 'present')->count();
                $totalCount = $attendances->count();
                
                return [
                    'participant' => $participant,
                    'total' => $totalCount,
                    'present' => $presentCount,
                    'absent' => $attendances->where('status', 'absent')->count(),
                    'late' => $attendances->where('status', 'late')->count(),
                    'excused' => $attendances->where('status', 'excused')->count(),
                    'percentage' => $totalCount > 0 
                        ? round(($presentCount / $totalCount) * 100, 2)
                        : 0
                ];
            });
        }

        return view('attendance.recap', compact('programs'));
    }
}
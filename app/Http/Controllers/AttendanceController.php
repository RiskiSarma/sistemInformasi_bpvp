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

    public function show(Program $program, Request $request)
    {
        $program->load(['masterProgram', 'participants']);

        // Tanggal yang dipilih untuk form absensi (default: hari ini)
        $selectedDate = $request->filled('date') 
            ? Carbon::parse($request->date) 
            : Carbon::today();

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
            ->values();

        // Group by date
        $attendances = $attendanceRecords->groupBy(function($item) {
            return $item->date instanceof Carbon 
                ? $item->date->format('Y-m-d') 
                : $item->date;
        });

        // Get existing attendance for selected date (untuk pre-fill form)
        $existingAttendances = Attendance::where('program_id', $program->id)
            ->whereDate('date', $selectedDate)
            ->get()
            ->keyBy('participant_id');

        return view('attendance.show', compact(
            'program', 
            'dates', 
            'attendances', 
            'selectedDate', 
            'existingAttendances'
        ));
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
            foreach ($validated['attendances'] as $attendanceData) {
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
                        'recorded_by' => auth()->id(),
                    ]
                );
            }
            
            DB::commit();
            return redirect()
                ->route('admin.attendance.show', [
                    'program' => $validated['program_id'],
                    'date' => $validated['date'],
                ])
                ->with('success', 'Kehadiran berhasil dicatat untuk tanggal ' . Carbon::parse($validated['date'])->format('d M Y') . '!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal mencatat kehadiran: ' . $e->getMessage());
        }
    }

    public function recap(Request $request)
    {
        $query = Program::with(['masterProgram', 'participants'])
            ->whereIn('status', ['ongoing', 'completed']);

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $programs = $query->orderBy('start_date', 'desc')->get();

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
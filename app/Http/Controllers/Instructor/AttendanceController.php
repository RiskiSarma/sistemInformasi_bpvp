<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Attendance;
use App\Models\Instructor;
use App\Models\Participant;
use App\Models\PengajarEksternal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function getInstructorAccess()
    {
        $user = auth()->user();

        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        return [$internal, $external];
    }

    private function applyInstructorConstraint($query, $internal, $external)
    {
        return $query->where(function($main) use ($internal, $external) {
            if ($internal) {
                $main->where(function($sub) use ($internal) {
                    $sub->where('instructor_id', $internal->id)
                        ->where('instructor_type', 'internal');
                });
            }
            if ($external) {
                $main->orWhere(function($sub) use ($external) {
                    $sub->where('pengajar_eksternal_id', $external->id);
                });
            }
        });
    }

    private function checkProgramAccess(Program $program, $internal, $external): bool
    {
        return $program->programInstructors()
            ->where(function($q) use ($internal, $external) {
                $this->applyInstructorConstraint($q, $internal, $external);
            })->exists();
    }

    public function index()
    {
        [$internal, $external] = $this->getInstructorAccess();

        if (!$internal && !$external) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }

        $programs = Program::whereHas('programInstructors', function($q) use ($internal, $external) {
            $this->applyInstructorConstraint($q, $internal, $external);
        })
        ->where('status', 'ongoing')
        ->with(['participants', 'masterProgram'])
        ->get();

        return view('instructor-area.attendance.index', compact('programs'));
    }

    public function show(Program $program)
    {
        [$internal, $external] = $this->getInstructorAccess();

        if (!$internal && !$external) {
            abort(403, 'Data instruktur tidak ditemukan');
        }

        if (!$this->checkProgramAccess($program, $internal, $external)) {
            abort(403, 'Anda tidak memiliki akses ke program ini');
        }

        $date = request('date', today()->format('Y-m-d'));
        $today = Carbon::parse($date);

        $participants = Participant::where('program_id', $program->id)
            ->with(['user', 'attendances' => function($query) use ($date) {
                $query->whereDate('date', $date);
            }])
            ->where('status', 'active')
            ->get();

        $attendances = Attendance::where('program_id', $program->id)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('participant_id');

        $attendanceHistory = Attendance::where('program_id', $program->id)
            ->select(
                'date',
                DB::raw("COUNT(CASE WHEN status = 'present' THEN 1 END) as present"),
                DB::raw("COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent"),
                DB::raw("COUNT(CASE WHEN status = 'excused' THEN 1 END) as excused"),
                DB::raw("COUNT(CASE WHEN status = 'late' THEN 1 END) as late"),
                DB::raw("COUNT(*) as total")
            )
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('instructor-area.attendance.show', compact(
            'program',
            'participants',
            'date',
            'attendances',
            'today',
            'attendanceHistory'
        ));
    }

    public function record(Request $request)
    {
        $request->validate([
            'program_id'                       => 'required|exists:programs,id',
            'date'                             => 'required|date',
            'attendances'                      => 'required|array',
            'attendances.*.participant_id'     => 'required|exists:participants,id',
            'attendances.*.status'             => 'required|in:present,absent,late,excused',
            'attendances.*.notes'              => 'nullable|string',
        ]);

        [$internal, $external] = $this->getInstructorAccess();

        if (!$internal && !$external) {
            return redirect()->back()->with('error', 'Data instruktur tidak ditemukan');
        }

        $program = Program::findOrFail($request->program_id);

        if (!$this->checkProgramAccess($program, $internal, $external)) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            foreach ($request->attendances as $attendanceData) {
                Attendance::updateOrCreate(
                    [
                        'program_id'     => $request->program_id,
                        'participant_id' => $attendanceData['participant_id'],
                        'date'           => $request->date,
                    ],
                    [
                        'status'      => $attendanceData['status'],
                        'notes'       => $attendanceData['notes'] ?? null,
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
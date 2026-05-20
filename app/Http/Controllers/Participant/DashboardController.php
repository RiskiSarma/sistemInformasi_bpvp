<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $allParticipants = Participant::with('program.masterProgram')
            ->where('user_id', $user->id)
            ->get();

        if ($allParticipants->isEmpty()) {
            return view('participant-area.dashboard', [
                'participant'          => null,
                'allParticipants'      => collect(),
                'program'              => null,
                'attendancePercentage' => 0,
                'totalAttendances'     => 0,
                'presentCount'         => 0,
                'absentCount'          => 0,
                'lateCount'            => 0,
                'excusedCount'         => 0,
                'recentAttendances'    => collect(),
            ]);
        }

        // Pilih participant aktif: dari query param, atau status active, atau yang pertama
        $selectedId  = $request->get('participant_id');
        $participant = $selectedId
            ? ($allParticipants->firstWhere('id', $selectedId) ?? $allParticipants->first())
            : ($allParticipants->firstWhere('status', 'active') ?? $allParticipants->first());

        $program = $participant->program;
        $program?->load(['masterProgram', 'instructor']);

        $base = Attendance::where('participant_id', $participant->id);

        $totalAttendances     = (clone $base)->count();
        $presentCount         = (clone $base)->where('status', 'present')->count();
        $absentCount          = (clone $base)->where('status', 'absent')->count();
        $lateCount            = (clone $base)->where('status', 'late')->count();
        $excusedCount         = (clone $base)->where('status', 'excused')->count();
        $attendancePercentage = $totalAttendances > 0
            ? round(($presentCount / $totalAttendances) * 100, 2)
            : 0;

        $recentAttendances = Attendance::where('participant_id', $participant->id)
            ->with('program.masterProgram')
            ->latest('date')
            ->take(5)
            ->get();

        return view('participant-area.dashboard', compact(
            'participant', 'allParticipants', 'program',
            'attendancePercentage', 'totalAttendances',
            'presentCount', 'absentCount', 'lateCount', 'excusedCount',
            'recentAttendances'
        ));
    }
}
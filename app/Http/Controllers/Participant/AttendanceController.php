<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $allParticipants = Participant::with('program.masterProgram')
            ->where('user_id', $user->id)
            ->get();

        if ($allParticipants->isEmpty()) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Data peserta tidak ditemukan');
        }

        $participantIds = $allParticipants->pluck('id');

        // Filter by participant/program tertentu jika dipilih
        $selectedParticipantId = $request->get('participant_id');
        $filterIds = $selectedParticipantId
            ? collect([$selectedParticipantId])
            : $participantIds;

        $attendances = Attendance::whereIn('participant_id', $filterIds)
            ->with('program.masterProgram', 
            'program.paketPelatihan',      // ← tambah
        'participant.program.masterProgram',) // ← fallback jika program di attendance berbeda
            ->orderBy('date', 'desc')
            ->paginate(20);

        // Hitung statistik dari semua data (bukan hanya halaman ini)
        $base             = Attendance::whereIn('participant_id', $filterIds);
        $totalAll         = (clone $base)->count();
        $totalHadir       = (clone $base)->where('status', 'present')->count();
        $totalAbsen       = (clone $base)->where('status', 'absent')->count();
        $totalTerlambat   = (clone $base)->where('status', 'late')->count();
        $totalIzin        = (clone $base)->where('status', 'excused')->count();
        $attendancePercentage = $totalAll > 0 ? round(($totalHadir / $totalAll) * 100) : 0;

        return view('participant-area.attendance.index', compact(
            'attendances', 'allParticipants', 'selectedParticipantId',
            'attendancePercentage', 'totalAll', 'totalHadir',
            'totalAbsen', 'totalTerlambat', 'totalIzin'
        ));
    }
}
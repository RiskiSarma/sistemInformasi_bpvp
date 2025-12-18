<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cari data participant dari tabel participants
        $participant = Participant::where('user_id', $user->id)->first();
        
        if (!$participant) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Data peserta tidak ditemukan');
        }
        
        // Ambil data kehadiran dari tabel attendances
        $attendances = Attendance::where('participant_id', $participant->id)
            ->with('program')
            ->orderBy('date', 'desc')
            ->paginate(20);
        
        // Statistics
        $stats = [
            'total' => Attendance::where('participant_id', $participant->id)->count(),
            'present' => Attendance::where('participant_id', $participant->id)
                ->where('status', 'present')->count(),
            'absent' => Attendance::where('participant_id', $participant->id)
                ->where('status', 'absent')->count(),
            'late' => Attendance::where('participant_id', $participant->id)
                ->where('status', 'late')->count(),
            'excused' => Attendance::where('participant_id', $participant->id)
                ->where('status', 'excused')->count(),
        ];
        
        // Calculate percentage
        $attendancePercentage = $stats['total'] > 0 
            ? round(($stats['present'] / $stats['total']) * 100, 2) 
            : 0;
        
        return view('participant-area.attendance.index', compact('attendances', 'stats', 'participant', 'attendancePercentage'));
    }
}
<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Participant;
use App\Models\Attendance;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Cari data participant dari tabel participants dengan eager loading
        $participant = Participant::with('program.masterProgram', 'program.instructor')
            ->where('user_id', $user->id)
            ->first();
        
        if (!$participant) {
            return view('participant-area.dashboard', [
                'participant' => null,
                'program' => null,
                'attendancePercentage' => 0,
                'totalAttendances' => 0,
                'presentCount' => 0,
                'absentCount' => 0,
                'lateCount' => 0,
                'excusedCount' => 0,
                'recentAttendances' => collect([]),
                'certificates' => collect([]),
                'error' => 'Data peserta tidak ditemukan. Hubungi administrator.'
            ]);
        }
        
        // Program yang diikuti dari tabel programs
        $program = $participant->program;
        
        // Statistik kehadiran dari tabel attendances
        $totalAttendances = Attendance::where('participant_id', $participant->id)->count();
        
        $presentCount = Attendance::where('participant_id', $participant->id)
            ->where('status', 'present')
            ->count();
        
        $absentCount = Attendance::where('participant_id', $participant->id)
            ->where('status', 'absent')
            ->count();
        
        $lateCount = Attendance::where('participant_id', $participant->id)
            ->where('status', 'late')
            ->count();
        
        $excusedCount = Attendance::where('participant_id', $participant->id)
            ->where('status', 'excused')
            ->count();
        
        $attendancePercentage = $totalAttendances > 0 
            ? round(($presentCount / $totalAttendances) * 100, 2) 
            : 0;
        
        // Recent attendances (5 terbaru)
        $recentAttendances = Attendance::where('participant_id', $participant->id)
            ->with('program.masterProgram')
            ->latest('date')
            ->take(5)
            ->get();
        
        // Sertifikat dari tabel certificates
        $certificates = Certificate::where('participant_id', $participant->id)->get();
        
        return view('participant-area.dashboard', compact(
            'participant',
            'program',
            'attendancePercentage',
            'totalAttendances',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount',
            'recentAttendances',
            'certificates'
        ));
    }
}
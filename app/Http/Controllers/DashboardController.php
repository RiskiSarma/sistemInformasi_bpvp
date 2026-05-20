<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Participant;
use App\Models\Instructor;
use App\Models\Certificate;
use App\Models\PengajarEksternal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'total_program_instances' => Program::count(),
            'total_participants' => Participant::count(),
            'active_instructors' => Instructor::where('status', 'active')->count(),
            'active_external_pengajar' => PengajarEksternal::count(),
            'certificates_issued' => Certificate::where('status', 'issued')->count(),
        ];

        $stats['total_active_teaching_staff'] = 
            $stats['active_instructors'] + $stats['active_external_pengajar'];

        // Recent Programs (5 terbaru)
        $recentPrograms = Program::with('masterProgram')
            ->latest()
            ->take(5)
            ->get();

        // Recent Participants (5 terbaru)
        $recentParticipants = Participant::with('program.masterProgram')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentPrograms', 'recentParticipants'));
    }
}
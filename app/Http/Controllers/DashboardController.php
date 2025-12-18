<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Participant;
use App\Models\Instructor;
use App\Models\Certificate;
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
            'certificates_issued' => Certificate::where('status', 'issued')->count(),
        ];

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
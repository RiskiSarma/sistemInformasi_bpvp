<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Attendance;
use App\Models\Instructor;
use App\Models\PengajarEksternal;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Debug: Log user info
        Log::info('Dashboard - User ID: ' . $user->id . ', Email: ' . $user->email);
        
        $internal = Instructor::where('user_id', $user->id)->first();
        $external = PengajarEksternal::where('user_id', $user->id)
                    ->orWhere('email', $user->email)
                    ->first();

        // Debug: Log instructor info
        if ($internal) {
            Log::info('Dashboard - Internal Instructor ID: ' . $internal->id);
        }
        if ($external) {
            Log::info('Dashboard - External Instructor ID: ' . $external->id);
        }

        if (!$internal && !$external) {
            Log::warning('Dashboard - No instructor found for user: ' . $user->id);
            return view('instructor-area.dashboard', [
                'totalPrograms'     => 0,
                'activePrograms'    => 0,
                'totalParticipants' => 0,
                'recentAttendances' => collect([]),
                'error' => 'Data instruktur tidak ditemukan.'
            ]);
        }

        // Query dengan debug
        $programs = Program::whereHas('programInstructors', function($q) use ($internal, $external) {
            $q->where(function($main) use ($internal, $external) {
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
        })
        ->with(['participants', 'masterProgram', 'paketPelatihan'])
        ->get();

        // Debug: Log programs found
        Log::info('Dashboard - Programs found: ' . $programs->count());
        foreach ($programs as $program) {
            Log::info('Dashboard - Program ID: ' . $program->id . ', Name: ' . optional($program->masterProgram)->name);
        }

        $totalPrograms = $programs->count();
        $activePrograms = $programs->where('status', 'ongoing')->count();
        $totalParticipants = $programs->sum(fn($p) => $p->participants->count());

        $recentAttendances = collect([]);

        return view('instructor-area.dashboard', compact(
            'totalPrograms', 
            'activePrograms', 
            'totalParticipants', 
            'recentAttendances'
        ));
    }
}
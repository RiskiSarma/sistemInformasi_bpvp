<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $instructor = auth()->user();
        
        // Get programs assigned to this instructor
        $programs = Program::where('instructor_id', $instructor->id)
            ->with(['participants'])
            ->get();
        
        // Statistics
        $totalPrograms = $programs->count();
        $activePrograms = $programs->where('status', 'active')->count();
        $totalParticipants = $programs->sum(function($program) {
            return $program->participants->count();
        });
        
        // Recent attendance
        $recentAttendances = Attendance::whereIn('program_id', $programs->pluck('id'))
            ->with(['participant', 'program'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('instructor-area.dashboard', compact(
            'programs',
            'totalPrograms',
            'activePrograms',
            'totalParticipants',
            'recentAttendances'
        ));
    }
}
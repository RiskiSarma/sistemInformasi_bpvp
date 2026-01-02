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
        $user = auth()->user();
        
        // Ambil data instruktur berdasarkan user yang login
        $instructor = \App\Models\Instructor::where('user_id', $user->id)->first();
        
        if (!$instructor) {
            // Opsional: handle jika belum ada data instruktur
            return view('instructor-area.dashboard', [
                'totalPrograms' => 0,
                'activePrograms' => 0,
                'totalParticipants' => 0,
                'recentAttendances' => collect([]),
                'error' => 'Data instruktur belum ditemukan. Hubungi admin.'
            ]);
        }

        // Get programs assigned to this instructor
        $programs = Program::where('instructor_id', $instructor->id)
            ->with(['participants'])
            ->get();
        
        // Statistics
        $totalPrograms = $programs->count();
        
        // Ubah 'active' jadi sesuai status yang benar di DB kamu (lihat catatan di bawah)
        $activePrograms = $programs->where('status', 'ongoing')->count(); // atau 'active', 'berjalan', dll.
        
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
            'totalPrograms',
            'activePrograms',
            'totalParticipants',
            'recentAttendances'
        ));
    }
}
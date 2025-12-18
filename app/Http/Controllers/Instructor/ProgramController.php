<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use App\Models\Instructor;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Cari instructor berdasarkan user_id dari tabel instructors
        $instructor = Instructor::where('user_id', $user->id)->first();
        
        if (!$instructor) {
            return redirect()->route('instructor.dashboard')
                ->with('error', 'Data instruktur tidak ditemukan');
        }
        
        // Query programs dari tabel programs
        $query = Program::where('instructor_id', $instructor->id);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $programs = $query->with(['participants', 'masterProgram'])
            ->orderBy('start_date', 'desc')
            ->paginate(9);
        
        return view('instructor-area.programs.index', compact('programs'));
    }
    
    public function show(Program $program)
    {
        $user = auth()->user();
        
        // Cari instructor dari tabel instructors
        $instructor = Instructor::where('user_id', $user->id)->first();
        
        // Pastikan program ini diajar oleh instructor yang login
        if (!$instructor || $program->instructor_id !== $instructor->id) {
            abort(403, 'Anda tidak memiliki akses ke program ini');
        }
        
        $program->load(['participants.user', 'participants.attendances', 'masterProgram', 'instructor', 'attendances']);
        
        return view('instructor-area.programs.show', compact('program'));
    }
}
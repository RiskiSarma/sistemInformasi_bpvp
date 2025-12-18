<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;

class ScheduleController extends Controller
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
        
        // Ambil program
        $program = $participant->program;
        
        if (!$program) {
            $message = 'Anda belum terdaftar di program manapun';
            return view('participant-area.schedule', compact('message', 'program'));
        }
        
        $program->load(['masterProgram', 'instructor.user']);
        
        return view('participant-area.schedule', compact('program', 'participant'));
    }
}
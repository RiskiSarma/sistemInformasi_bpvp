<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;

class ProgramController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        // Cari data participant dari tabel participants
        $participant = Participant::where('user_id', $user->id)->first();
        
        if (!$participant) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Data peserta tidak ditemukan');
        }
        
        // Ambil program dari tabel programs
        $program = $participant->program;
        
        if (!$program) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Anda belum terdaftar di program manapun');
        }
        
        $program->load(['masterProgram', 'instructor']);
        
        return view('participant-area.program.show', compact('program', 'participant'));
    }
}
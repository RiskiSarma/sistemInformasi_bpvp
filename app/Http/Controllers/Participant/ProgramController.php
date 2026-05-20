<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\Participant;

class ProgramController extends Controller
{
    public function index()
    {
        $participants = Participant::with('program.masterProgram', 'program.instructor')
            ->where('user_id', auth()->id())
            ->get();

        // Jika hanya 1 program, langsung ke detail
        // if ($participants->count() === 1) {
        //     return $this->renderShow($participants->first(), $participants);
        // }

        return view('participant-area.program.index', compact('participants'));
    }

    public function show(Participant $participant)
    {
        abort_if($participant->user_id !== auth()->id(), 403);

        $participant->load('program.masterProgram', 'program.instructor');

        $allParticipants = Participant::with('program.masterProgram')
            ->where('user_id', auth()->id())
            ->get();

        return $this->renderShow($participant, $allParticipants);
    }

    private function renderShow(Participant $participant, $allParticipants)
    {
        $program = $participant->program;
        $program?->load(['masterProgram', 'instructor']);

        return view('participant-area.program.show', compact('program', 'participant', 'allParticipants'));
    }
}
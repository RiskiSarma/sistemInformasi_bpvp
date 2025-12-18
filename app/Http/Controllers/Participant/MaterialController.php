<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;

class MaterialController extends Controller
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
        
        // TODO: Implementasi materials jika ada tabel materials
        $materials = collect([]); // Sementara kosong
        
        return view('participant-area.material', compact('participant', 'program', 'materials'));
    }
}
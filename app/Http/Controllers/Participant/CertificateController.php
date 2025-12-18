<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Certificate;

class CertificateController extends Controller
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
        
        // Ambil sertifikat dari tabel certificates
        $certificates = Certificate::where('participant_id', $participant->id)
            ->with('program')
            ->latest()
            ->get();
        
        return view('participant-area.certificate.index', compact('certificates', 'participant'));
    }
    
    public function download($id = null)
    {
        $user = auth()->user();
        
        // Cari data participant dari tabel participants
        $participant = Participant::where('user_id', $user->id)->first();
        
        if (!$participant) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Data peserta tidak ditemukan');
        }
        
        // Jika ada ID spesifik, cari berdasarkan ID
        if ($id) {
            $certificate = Certificate::where('participant_id', $participant->id)
                ->where('id', $id)
                ->first();
        } else {
            // Jika tidak, ambil sertifikat terbaru yang aktif
            $certificate = Certificate::where('participant_id', $participant->id)
                ->where('status', 'issued')
                ->latest()
                ->first();
        }
        
        if (!$certificate) {
            return redirect()->back()->with('error', 'Sertifikat tidak ditemukan');
        }
        
        // Check if file exists
        if ($certificate->file_path && file_exists(storage_path('app/public/' . $certificate->file_path))) {
            return response()->download(
                storage_path('app/public/' . $certificate->file_path),
                'Sertifikat_' . $participant->user->name . '.pdf'
            );
        }
        
        // If no file, redirect with info
        return redirect()->back()->with('info', 'File sertifikat sedang diproses. Silakan coba lagi nanti.');
    }
}   
<?php

namespace App\Http\Controllers\Participant;

use Illuminate\Support\Facades\Storage;
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
    
    public function download(Certificate $certificate)
    {
        $user = auth()->user();
        
        // Cari participant dari user
        $participant = Participant::where('user_id', $user->id)->first();
        
        if (!$participant) {
            return redirect()->route('participant.dashboard')
                ->with('error', 'Data peserta tidak ditemukan');
        }
        
        // Pastikan sertifikat milik participant ini
        if ($certificate->participant_id !== $participant->id) {
            abort(403, 'Akses ditolak: Sertifikat bukan milik Anda');
        }
        
        // Cek file ada atau tidak
        if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            $safeFilename = str_replace(['/', '\\'], '-', $certificate->certificate_number) . '.pdf';
            return Storage::download($certificate->pdf_path, "Sertifikat_{$safeFilename}");
        }
        
        // Kalau file hilang, regenerate (opsional, panggil method dari CertificateController)
        app(\App\Http\Controllers\CertificateController::class)->generatePDF($certificate);
        
        // Coba download lagi setelah regenerate
        if (Storage::exists($certificate->pdf_path)) {
            $safeFilename = str_replace(['/', '\\'], '-', $certificate->certificate_number) . '.pdf';
            return Storage::download($certificate->pdf_path, "Sertifikat_{$safeFilename}");
        }
        
        return redirect()->back()->with('error', 'File sertifikat tidak ditemukan. Hubungi admin.');
    }
}   
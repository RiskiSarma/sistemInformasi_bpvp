<?php

namespace App\Http\Controllers;

use App\Models\ParticipantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DaftarUlangController extends Controller
{
    /**
     * List semua dokumen peserta (admin).
     */
    public function index(Request $request)
    {
        $query = ParticipantDocument::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $documents = $query->paginate(20);

        return view('daftar-ulang.index', compact('documents'));
    }

    /**
     * Approve dokumen (admin).
     */
    public function approve(ParticipantDocument $document)
    {
        $document->update([
            'status'      => 'approved',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'catatan'     => null,
        ]);

        return back()->with('success', 'Dokumen berhasil disetujui.');
    }

    /**
     * Reject dokumen (admin).
     */
    public function reject(Request $request, ParticipantDocument $document)
    {
        $request->validate(['catatan' => 'required|string|max:500']);

        $document->update([
            'status'      => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'catatan'     => $request->catatan,
        ]);

        return back()->with('success', 'Dokumen ditolak dengan catatan.');
    }

    /**
     * Preview file dokumen peserta (admin).
     */
    public function preview(ParticipantDocument $document)
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType    = Storage::disk('local')->mimeType($document->file_path);
        $fileContent = Storage::disk('local')->get($document->file_path);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
    }
}
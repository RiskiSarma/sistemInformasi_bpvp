<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\ParticipantDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DaftarUlangController extends Controller
{
    /**
     * Tampilkan halaman daftar ulang beserta status dokumen.
     */
    public function index()
    {
        $user = Auth::user();
        $requiredDocuments = ParticipantDocument::requiredDocuments();

        // Ambil semua dokumen yang sudah diupload user ini
        $uploadedDocuments = ParticipantDocument::where('user_id', $user->id)
            ->get()
            ->keyBy('document_type');

        // Hitung progress
        $totalRequired = count($requiredDocuments);
        $totalUploaded = $uploadedDocuments->count();
        $totalApproved = $uploadedDocuments->where('status', 'approved')->count();
        $progressPercent = $totalRequired > 0 ? round(($totalUploaded / $totalRequired) * 100) : 0;

        return view('participant-area.daftar-ulang.index', compact(
            'requiredDocuments',
            'uploadedDocuments',
            'totalRequired',
            'totalUploaded',
            'totalApproved',
            'progressPercent'
        ));
    }

    /**
     * Upload / ganti dokumen.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'document_type' => ['required', 'string', 'in:' . implode(',', array_keys(ParticipantDocument::requiredDocuments()))],
            'file'          => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // max 5MB
        ], [
            'file.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'file.max'   => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = Auth::user();
        $docType = $request->document_type;
        $docLabel = ParticipantDocument::requiredDocuments()[$docType];

        // Upload file ke storage
        $file = $request->file('file');
        $fileName = $user->id . '_' . $docType . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('participant-documents/' . $user->id, $fileName, 'local');
        $fileSize = $this->formatBytes($file->getSize());

        // Hapus file lama jika ada (re-upload)
        $existing = ParticipantDocument::where('user_id', $user->id)
            ->where('document_type', $docType)
            ->first();

        if ($existing) {
            // Hapus file lama dari storage
            if (Storage::disk('local')->exists($existing->file_path)) {
                Storage::disk('local')->delete($existing->file_path);
            }
            $existing->update([
                'file_path'   => $filePath,
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $fileSize,
                'status'      => 'pending', // Reset ke pending saat re-upload
                'catatan'     => null,
                'verified_at' => null,
                'verified_by' => null,
            ]);
        } else {
            ParticipantDocument::create([
                'user_id'       => $user->id,
                'document_type' => $docType,
                'document_label'=> $docLabel,
                'file_path'     => $filePath,
                'file_name'     => $file->getClientOriginalName(),
                'file_size'     => $fileSize,
                'status'        => 'pending',
            ]);
        }

        return redirect()->route('participant.daftar-ulang.index')
            ->with('success', "Dokumen {$docLabel} berhasil diupload.");
    }

    /**
     * Preview / unduh file dokumen peserta.
     */
    public function preview(ParticipantDocument $document)
    {
        // Pastikan dokumen milik user yang login
        abort_if($document->user_id !== Auth::id(), 403);

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = Storage::disk('local')->mimeType($document->file_path);
        $fileContent = Storage::disk('local')->get($document->file_path);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
    }

    /**
     * Hapus dokumen.
     */
    public function destroy(ParticipantDocument $document)
    {
        abort_if($document->user_id !== Auth::id(), 403);
        abort_if($document->status === 'approved', 403, 'Dokumen yang sudah disetujui tidak dapat dihapus.');

        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }

        $label = $document->document_label;
        $document->delete();

        return redirect()->route('participant.daftar-ulang.index')
            ->with('success', "Dokumen {$label} berhasil dihapus.");
    }

    // ================================================================
    // ADMIN: Verifikasi dokumen peserta
    // ================================================================

    /**
     * List semua dokumen peserta (admin).
     */
    public function adminIndex(Request $request)
    {
        $query = ParticipantDocument::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
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
     * Admin preview file dokumen peserta.
     */
    public function adminPreview(ParticipantDocument $document)
    {
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404);
        }

        $mimeType = Storage::disk('local')->mimeType($document->file_path);
        $fileContent = Storage::disk('local')->get($document->file_path);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
    }

    // ================================================================
    // Helper
    // ================================================================
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
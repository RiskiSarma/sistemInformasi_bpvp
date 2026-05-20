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

        $uploadedDocuments = ParticipantDocument::where('user_id', $user->id)
            ->get()
            ->keyBy('document_type');

        $totalRequired   = count($requiredDocuments);
        $totalUploaded   = $uploadedDocuments->count();
        $totalApproved   = $uploadedDocuments->where('status', 'approved')->count();
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
            'file'          => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'file.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'file.max'   => 'Ukuran file maksimal 5 MB.',
        ]);

        $user     = Auth::user();
        $docType  = $request->document_type;
        $docLabel = ParticipantDocument::requiredDocuments()[$docType];

        $file     = $request->file('file');
        $fileName = $user->id . '_' . $docType . '_' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('participant-documents/' . $user->id, $fileName, 'local');
        $fileSize = $this->formatBytes($file->getSize());

        // === AMBIL PROGRAM ID PESERTA ===
        // ✅ PERBAIKAN: Ambil programs_id dari relasi participant → program
        $programId  = null;
        $participant = $user->participant; // lazy load
        if ($participant) {
            $programId = $participant->program_id          // jika kolom langsung ada
                ?? $participant->program?->id              // via relasi
                ?? null;
        }

        $existing = ParticipantDocument::where('user_id', $user->id)
            ->where('document_type', $docType)
            ->first();

        if ($existing) {
            if (Storage::disk('local')->exists($existing->file_path)) {
                Storage::disk('local')->delete($existing->file_path);
            }

            // ✅ PERBAIKAN: programs_id ikut diupdate
            $existing->update([
                'programs_id' => $programId,
                'file_path'   => $filePath,
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $fileSize,
                'status'      => 'pending',
                'catatan'     => null,
                'verified_at' => null,
                'verified_by' => null,
            ]);
        } else {
            // ✅ PERBAIKAN: programs_id disimpan saat create
            ParticipantDocument::create([
                'user_id'        => $user->id,
                'programs_id'    => $programId,
                'document_type'  => $docType,
                'document_label' => $docLabel,
                'file_path'      => $filePath,
                'file_name'      => $file->getClientOriginalName(),
                'file_size'      => $fileSize,
                'status'         => 'pending',
            ]);
        }

        return redirect()->route('participant.daftar-ulang.index')
            ->with('success', "Dokumen {$docLabel} berhasil diupload.");
    }

    /**
     * Preview file dokumen milik peserta yang sedang login.
     */
    public function preview(ParticipantDocument $document)
    {
        abort_if($document->user_id !== Auth::id(), 403);

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType    = Storage::disk('local')->mimeType($document->file_path);
        $fileContent = Storage::disk('local')->get($document->file_path);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"');
    }

    /**
     * Hapus dokumen milik peserta yang sedang login.
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

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
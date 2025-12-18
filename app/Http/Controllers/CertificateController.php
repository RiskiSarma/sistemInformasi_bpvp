<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Program;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::with(['participant', 'program.masterProgram']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                  ->orWhereHas('participant', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $certificates = $query->orderBy('issue_date', 'desc')->paginate(15)->appends($request->all());
        
        return view('certificates.index', compact('certificates'));
    }

    public function create()
    {
        // Get only graduated participants who don't have certificates yet
        $participants = Participant::where('status', 'graduated')
            ->whereDoesntHave('certificate')
            ->with('program.masterProgram')
            ->get();
        
        // Generate suggested certificate number
        $suggestedNumber = Certificate::generateSuggestedNumber();
        
        return view('certificates.create', compact('participants', 'suggestedNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'certificate_number' => 'required|string|unique:certificates,certificate_number|max:100',
            'issue_date' => 'required|date',
            'notes' => 'nullable|string',
        ], [
            'certificate_number.required' => 'Nomor sertifikat harus diisi',
            'certificate_number.unique' => 'Nomor sertifikat sudah digunakan',
            'certificate_number.max' => 'Nomor sertifikat maksimal 100 karakter',
        ]);

        $participant = Participant::findOrFail($validated['participant_id']);

        // Check if participant is eligible
        if (!$participant->isEligibleForCertificate()) {
            return back()->with('error', 'Peserta tidak memenuhi syarat untuk menerima sertifikat. (Status: Lulus & Kehadiran min. 75%)');
        }

        // Check if certificate already exists
        if ($participant->certificate) {
            return back()->with('error', 'Sertifikat untuk peserta ini sudah pernah diterbitkan!');
        }

        // Create certificate
        $certificate = Certificate::create([
            'participant_id' => $validated['participant_id'],
            'program_id' => $participant->program_id,
            'certificate_number' => strtoupper(trim($validated['certificate_number'])),
            'issue_date' => $validated['issue_date'],
            'status' => 'issued',
            'notes' => $validated['notes'] ?? null,
        ]);

        // Generate PDF
        $this->generatePDF($certificate);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Sertifikat berhasil diterbitkan!');
    }

    public function bulkCreate(Request $request)
    {
        $programId = $request->get('program_id');
        
        $participants = Participant::where('status', 'graduated')
            ->whereDoesntHave('certificate')
            ->with('program.masterProgram')
            ->when($programId, function($q) use ($programId) {
                $q->where('program_id', $programId);
            })
            ->get();

        $programs = Program::with('masterProgram')->get();
        
        return view('certificates.bulk-create', compact('participants', 'programs'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:participants,id',
            'issue_date' => 'required|date',
            'certificate_numbers' => 'required|array',
            'certificate_numbers.*' => 'required|string|max:100',
        ], [
            'certificate_numbers.required' => 'Nomor sertifikat untuk setiap peserta harus diisi',
            'certificate_numbers.*.required' => 'Setiap nomor sertifikat harus diisi',
        ]);

        $issued = 0;
        $failed = [];

        foreach ($validated['participant_ids'] as $index => $participantId) {
            $participant = Participant::find($participantId);
            $certificateNumber = trim($validated['certificate_numbers'][$index]);

            // Skip if certificate number is empty
            if (empty($certificateNumber)) {
                $failed[] = $participant->name . ' (nomor sertifikat kosong)';
                continue;
            }

            // Check if certificate number already exists
            if (Certificate::where('certificate_number', $certificateNumber)->exists()) {
                $failed[] = $participant->name . ' (nomor sertifikat sudah digunakan: ' . $certificateNumber . ')';
                continue;
            }

            // Check eligibility
            if (!$participant->isEligibleForCertificate()) {
                $failed[] = $participant->name . ' (tidak memenuhi syarat)';
                continue;
            }

            // Check if already has certificate
            if ($participant->certificate) {
                $failed[] = $participant->name . ' (sudah memiliki sertifikat)';
                continue;
            }

            // Create certificate
            $certificate = Certificate::create([
                'participant_id' => $participantId,
                'program_id' => $participant->program_id,
                'certificate_number' => strtoupper($certificateNumber),
                'issue_date' => $validated['issue_date'],
                'status' => 'issued',
            ]);

            // Generate PDF
            $this->generatePDF($certificate);
            $issued++;
        }

        $message = "Berhasil menerbitkan {$issued} sertifikat.";
        if (count($failed) > 0) {
            $message .= " Gagal: " . implode(', ', $failed);
        }

        return redirect()->route('certificates.index')
            ->with('success', $message);
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['participant', 'program.masterProgram']);
        
        return view('certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
            // Regenerate PDF if not exists
            $this->generatePDF($certificate);
        }

        return Storage::download($certificate->pdf_path, 'Certificate_' . $certificate->certificate_number . '.pdf');
    }

    public function destroy(Certificate $certificate)
    {
        // Delete PDF file
        if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            Storage::delete($certificate->pdf_path);
        }

        $certificate->delete();
        
        return redirect()->route('certificates.index')
            ->with('success', 'Sertifikat berhasil dihapus!');
    }

    private function generatePDF(Certificate $certificate)
    {
        $certificate->load(['participant', 'program.masterProgram']);

        $pdf = PDF::loadView('certificates.template', compact('certificate'))
            ->setPaper('a4', 'landscape');

        $filename = 'certificates/' . str_replace('/', '_', $certificate->certificate_number) . '.pdf';
        Storage::put($filename, $pdf->output());

        $certificate->update(['pdf_path' => $filename]);

        return $pdf;
    }

    public function preview(Certificate $certificate)
    {
        $certificate->load(['participant', 'program.masterProgram']);

        $pdf = PDF::loadView('certificates.template', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Certificate_Preview.pdf');
    }
}
?>
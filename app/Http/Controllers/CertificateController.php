<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Program;
use App\Models\IndependentCompetencyUnit; 
use Illuminate\Http\Request;
use TCPDF as Pdf;
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
        $participants = Participant::where('status', 'graduated')
            ->whereDoesntHave('certificate')
            ->with([
                'program.masterProgram',
                'program.independentCompetencyUnit', 
                'attendances'
            ])
            ->get();

        // Opsional: hitung persentase
        $participants = $participants->map(function ($participant) {
            $participant->calculated_percentage = $participant->getAttendancePercentage();
            return $participant;
        });

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

        if (!$participant->isEligibleForCertificate()) {
            return back()->with('error', 'Peserta tidak memenuhi syarat untuk menerima sertifikat. (Status: Lulus & Kehadiran min. 75%)');
        }

        if ($participant->certificate) {
            return back()->with('error', 'Sertifikat untuk peserta ini sudah pernah diterbitkan!');
        }

        $certificate = Certificate::create([
            'participant_id' => $validated['participant_id'],
            'program_id' => $participant->program_id,
            'certificate_number' => strtoupper(trim($validated['certificate_number'])),
            'issue_date' => $validated['issue_date'],
            'status' => 'issued',
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->generatePDF($certificate);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Sertifikat berhasil diterbitkan!');
    }

    public function bulkCreate(Request $request)
    {
        $programId = $request->get('program_id');
        
        $participants = Participant::where('status', 'graduated')
            ->whereDoesntHave('certificate')
            ->with(['program.masterProgram', 'program.independentCompetencyUnit'])
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

            if (empty($certificateNumber)) {
                $failed[] = $participant->name . ' (nomor sertifikat kosong)';
                continue;
            }

            if (Certificate::where('certificate_number', $certificateNumber)->exists()) {
                $failed[] = $participant->name . ' (nomor sertifikat sudah digunakan: ' . $certificateNumber . ')';
                continue;
            }

            if (!$participant->isEligibleForCertificate()) {
                $failed[] = $participant->name . ' (tidak memenuhi syarat)';
                continue;
            }

            if ($participant->certificate) {
                $failed[] = $participant->name . ' (sudah memiliki sertifikat)';
                continue;
            }

            $certificate = Certificate::create([
                'participant_id' => $participantId,
                'program_id' => $participant->program_id,
                'certificate_number' => strtoupper($certificateNumber),
                'issue_date' => $validated['issue_date'],
                'status' => 'issued',
            ]);

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
        $certificate->load(['participant', 'program.masterProgram', 'program.independentCompetencyUnits']);
        
        return view('certificates.show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        if (!$certificate->pdf_path || !Storage::exists($certificate->pdf_path)) {
            $this->generatePDF($certificate);
        }

        $safeFilename = str_replace(['/', '\\'], '-', $certificate->certificate_number) . '.pdf';
        
        return Storage::download($certificate->pdf_path, "Sertifikat_{$safeFilename}");
    }

    public function destroy(Certificate $certificate)
    {
        if ($certificate->pdf_path && Storage::exists($certificate->pdf_path)) {
            Storage::delete($certificate->pdf_path);
        }

        $certificate->delete();
        
        return redirect()->route('admin.certificates.index')
            ->with('success', 'Sertifikat berhasil dihapus!');
    }

    private function generatePDF(Certificate $certificate)
    {
        $certificate->load(['participant', 'program.masterProgram', 'program.independentCompetencyUnits']);

        $pdf = new Pdf('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->AddPage();

        // ========== BACKGROUND BORDER ==========
        if (file_exists(public_path('images/border-blue-microtext.jpg'))) {
            $pdf->Image(public_path('images/border-blue-microtext.jpg'), 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
        }

        // ========== LOGO KIRI ATAS ==========
        if (file_exists(public_path('images/logo blk banda.png'))) {
            $pdf->Image(public_path('images/logo blk banda.png'), 12, 10, 25, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }

        // ========== LOGO KANAN ATAS ==========
        if (file_exists(public_path('images/logo-black.png'))) {
            $pdf->Image(public_path('images/logo-black.png'), 260, 20, 25, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }

        // ========== LOGO EMAS KIRI BAWAH ==========
        if (file_exists(public_path('images/logo-kuning.jpg'))) {
            $pdf->Image(public_path('images/logo-kuning.jpg'), 12, 175, 32, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
        }

        // ========== HEADER TEXT (3 BARIS) ==========
        $pdf->SetTextColor(0, 0, 0);
        
        $pdf->SetFont('times', '', 12);
        $pdf->SetXY(45, 10);
        $pdf->Cell(207, 4, 'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        
        $pdf->SetXY(45, 14);
        $pdf->Cell(207, 4, 'DIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetXY(45, 18);
        $pdf->Cell(207, 4, 'BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // ========== JUDUL SERTIFIKAT ==========
        $pdf->SetFont('times', '', 22);
        $pdf->SetXY(0, 40);
        $pdf->Cell(297, 8, 'SERTIFIKAT KELULUSAN PELATIHAN', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // ========== NOMOR SERTIFIKAT ==========
        $pdf->SetFont('times', '', 12);
        $pdf->SetXY(0, 52);
        $pdf->Cell(297, 5, 'NOMOR: ' . $certificate->certificate_number, 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // ========== PERNYATAAN ==========
        $pdf->SetFont('times', '', 14);
        $pdf->SetXY(50, 62);
        $pdf->Cell(100, 5, 'Dengan ini menyatakan bahwa:', 0, 0, 'L', false, '', 0, false, 'T', 'M');

        // ========== DATA FIELDS ==========
        $yStart = 72;
        
        $pdf->SetFont('times', '', 14);
        $pdf->SetXY(70, $yStart);
        $pdf->Cell(55, 5, 'Nama', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->SetFont('times', 'B', 14);
        $pdf->Cell(0, 5, ': ' . strtoupper($certificate->participant->name ?? '-'), 0, 0, 'L', false, '', 0, false, 'T', 'M');

        $pdf->SetFont('times', '', 14);
        $pdf->SetXY(70, $yStart + 7);
        $pdf->Cell(55, 5, 'NIK', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 5, ': ' . ($certificate->participant->nik ?? '-'), 0, 0, 'L', false, '', 0, false, 'T', 'M');

        $pdf->SetXY(70, $yStart + 14);
        $pdf->Cell(55, 5, 'Tempat, tanggal lahir', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $birthPlace = strtoupper($certificate->participant->birth_place ?? '-');
        $birthDate = $certificate->participant->birth_date 
            ? \Carbon\Carbon::parse($certificate->participant->birth_date)->isoFormat('DD MMMM YYYY') 
            : '-';
        $pdf->Cell(0, 5, ': ' . $birthPlace . ', ' . $birthDate, 0, 0, 'L', false, '', 0, false, 'T', 'M');

        // ========== DESKRIPSI PELATIHAN ==========
        $startDate = $certificate->program->start_date 
            ? \Carbon\Carbon::parse($certificate->program->start_date)->isoFormat('DD MMMM YYYY') 
            : '27 Oktober 2025';
        $endDate = $certificate->program->end_date 
            ? \Carbon\Carbon::parse($certificate->program->end_date)->isoFormat('DD MMMM YYYY') 
            : '01 Desember 2025';
        $duration = $certificate->program->duration ?? '260';
        $programName = strtoupper($certificate->program->masterProgram->name ?? 'N/A');

        // Nama program + angkatan (contoh: Hydroponic Automatic System I)
        $programName = $certificate->program->masterProgram->name . ' ' . $certificate->program->angkatan;

        // Ambil data dinamis dari master program
        $kejuruan       = $certificate->program->masterProgram->kejuruan ?? 'Tidak Diketahui';
        $bidang         = $certificate->program->masterProgram->bidang ?? 'Tidak Diketahui';
        $jenisPelatihan = $certificate->program->masterProgram->jenis_pelatihan_full ?? 
                        $certificate->program->masterProgram->jenis_pelatihan ?? 'Tidak Diketahui';

        // Teks deskripsi sepenuhnya dinamis (termasuk kejuruan)
        $fullText = 'telah menyelesaikan Pelatihan ' . $jenisPelatihan . 
                    ' Bidang ' . $bidang . 
                    ' untuk program <b>' . $programName . 
                    ' Kejuruan ' . $kejuruan . 
                    '</b> yang dilaksanakan pada tanggal ' . 
                    $startDate . ' sampai dengan ' . $endDate . 
                    ' selama ' . $duration . ' Jam Pelatihan dan dinyatakan <b>LULUS</b>';
        
        $pdf->SetFont('times', '', 14);
        $pdf->SetXY(50, 95);
        $pdf->writeHTMLCell(197, 0, '', '', $fullText, 0, 1, false, true, 'L', true);

        // ========== TEMPAT & TANGGAL ==========
        $pdf->SetFont('times', '', 10);
        $issueDate = \Carbon\Carbon::parse($certificate->issue_date)->isoFormat('DD MMMM YYYY');
        $pdf->SetXY(177, 120);
        $pdf->Cell(60, 5, 'Banda Aceh, ' . $issueDate, 0, 1, 'C', false, '', 0, false, 'T', 'M');
        $pdf->SetXY(177, 125);
        $pdf->Cell(60, 5, 'Kepala Balai Pelatihan Vokasi', 0, 1, 'C', false, '', 0, false, 'T', 'M');
        $pdf->SetXY(177, 130);
        $pdf->Cell(60, 5, 'dan Produktivitas Banda Aceh,', 0, 1, 'C', false, '', 0, false, 'T', 'M');

        // ========== QR CODE ==========
        $qrLink = route('admin.certificates.certificate.verify', $certificate->certificate_number);
        $pdf->write2DBarcode($qrLink, 'QRCODE,L', 197, 140, 20, 20, array('border' => false), 'N');

        // ========== TANDA TANGAN ==========
        $pdf->SetFont('times', '', 10);
        $pdf->SetXY(177, 166);
        $pdf->Cell(60, 5, 'Rahmad Faisal', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        
        $pdf->SetFont('times', '', 9);
        $pdf->SetXY(177, 171);
        $pdf->Cell(60, 5, 'NIP 19810330 200901 1 005', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // ========== FOOTER ==========
        $pdf->SetFont('times', '', 6.5);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetXY(30, 198);
        $pdf->Cell(237, 3, 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        $pdf->SetXY(30, 201);
        $pdf->Cell(237, 3, 'yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // ==================== HALAMAN 2: DAFTAR UNIT KOMPETENSI ====================
        $pdf->AddPage();

        $pdf->SetFont('times', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, 20);
        $pdf->Cell(297, 8, 'DAFTAR UNIT KOMPETENSI', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // Tabel Header
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetXY(30, 35);
        $pdf->Cell(20, 10, 'No', 1, 0, 'C', true);
        $pdf->Cell(140, 10, 'Unit Kompetensi', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Kode Unit', 1, 0, 'C', true);
        $pdf->Cell(37, 10, 'Lulus/Belum Lulus', 1, 1, 'C', true);

        // Data Rows - loop semua unit
        $pdf->SetFont('times', '', 9);
        $yPos = 45;
        $no = 1;

        $units = $certificate->program->independentCompetencyUnits;

        if ($units->count() > 0) {
            foreach ($units as $unit) {
                $startY = $yPos;

                // Nama unit (MultiCell untuk wrap teks panjang)
                $pdf->SetXY(50, $yPos);
                $pdf->MultiCell(140, 5, $unit->name ?? '-', 0, 'L');
                $endY = $pdf->GetY();
                $rowHeight = $endY - $startY;

                // Border dan teks kolom lain
                $pdf->SetXY(30, $startY);
                $pdf->Cell(20, $rowHeight, $no, 1, 0, 'C');

                $pdf->SetXY(50, $startY);
                $pdf->MultiCell(140, 5, $unit->name ?? '-', 1, 'L');

                $pdf->SetXY(190, $startY);
                $pdf->Cell(60, $rowHeight, $unit->code ?? '-', 1, 0, 'C');

                $pdf->SetXY(250, $startY);
                $pdf->Cell(37, $rowHeight, 'LULUS', 1, 0, 'C');

                $yPos = $endY;
                $no++;
            }
        } else {
            $pdf->SetXY(30, $yPos);
            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(257, 10, 'Tidak ada unit kompetensi untuk program ini', 1, 0, 'C', false);
        }

        // === CODE LAMA (dikomentari, tetap aman untuk rollback) ===
        /*
        // Ambil data unit kompetensi dari master_program_id (sistem lama)
        $masterProgramId = $certificate->program->master_program_id ?? null;
        $competencyUnits = null;
        
        if ($masterProgramId) {
            $competencyUnits = \App\Models\CompetencyUnit::where('master_program_id', $masterProgramId)->get();
        }

        if ($competencyUnits && $competencyUnits->count() > 0) {
            foreach ($competencyUnits as $unit) {
                if ($yPos > 180) break;
                
                $startY = $yPos;
                
                $pdf->SetXY(50, $yPos);
                $pdf->MultiCell(140, 5, $unit->name ?? '-', 0, 'L');
                $endY = $pdf->GetY();

                $rowHeight = $endY - $startY;
                
                $pdf->SetXY(30, $startY);
                $pdf->Cell(20, $rowHeight, $no, 1, 0, 'C');

                $pdf->SetXY(50, $startY);
                $pdf->MultiCell(140, 5, $unit->name ?? '-', 1, 'L');

                $pdf->SetXY(190, $startY);
                $pdf->Cell(60, $rowHeight, $unit->code ?? '-', 1, 0, 'C');

                $pdf->SetXY(250, $startY);
                $pdf->Cell(37, $rowHeight, 'LULUS', 1, 0, 'C');

                $yPos = $startY + $rowHeight;
                $no++;
            }
        } else {
            $pdf->SetXY(30, $yPos);
            $pdf->SetFont('times', 'I', 10);
            $pdf->Cell(257, 10, 'Tidak ada data unit kompetensi untuk program ini (sistem lama)', 1, 0, 'C', false);
        }
        */

        // Footer Halaman 2
        $pdf->SetFont('times', '', 6.5);
        $pdf->SetTextColor(51, 51, 51);
        $pdf->SetXY(30, 195);
        $pdf->Cell(237, 3, 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        $pdf->SetXY(30, 198);
        $pdf->Cell(237, 3, 'yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).', 0, 0, 'C', false, '', 0, false, 'T', 'M');

        // Simpan PDF
        $filename = 'certificates/Sertifikat_' . str_replace(['/', '\\'], '-', $certificate->certificate_number) . '.pdf';
        Storage::put($filename, $pdf->Output('', 'S'));

        $certificate->update(['pdf_path' => $filename]);

        return $pdf->Output('', 'S');
    }

    public function preview(Certificate $certificate)
    {
        $certificate->load(['participant', 'program.masterProgram', 'program.independentCompetencyUnits']);

        $pdfContent = $this->generatePDF($certificate);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Sertifikat_Preview_' . str_replace(['/', '\\'], '-', $certificate->certificate_number) . '.pdf"');
    }

    public function verify($certificate_number)
    {
        $certificate = Certificate::where('certificate_number', $certificate_number)
            ->with(['participant', 'program.masterProgram', 'program.independentCompetencyUnit'])
            ->first();

        if (!$certificate) {
            return view('certificates.verify-failed', [
                'message' => 'Sertifikat dengan nomor ' . $certificate_number . ' tidak ditemukan.',
            ]);
        }

        if ($certificate->status !== 'issued') {
            return view('certificates.verify-failed', [
                'message' => 'Sertifikat ini tidak valid (status: ' . ucfirst($certificate->status) . ').',
            ]);
        }

        return view('certificates.verify-success', compact('certificate'));
    }
}
?>
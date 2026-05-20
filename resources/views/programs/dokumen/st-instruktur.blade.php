<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penugasan Instruktur</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 20mm 25mm;
        }

        .kop-wrap img { width: 100%; height: auto; }
        .judul-dok { text-align: center; margin: 20px 0 10px; }
        .judul-dok h2 { font-size: 13pt; font-weight: bold; }
        .nomor-st { text-align: center; font-size: 11.5pt; margin-bottom: 20px; }

        .tabel-unit { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11pt; }
        .tabel-unit th, .tabel-unit td { border: 1px solid #000; padding: 6px 8px; }
        .tabel-unit th { background: #f5f5f5; text-align: center; font-weight: bold; }
        .bullet-check::before { content: "❖ "; }

        .ttd-area { margin-top: 40px; text-align: right; }
        .footer-elektronik {
            position: fixed; bottom: 10mm; left: 25mm; right: 25mm;
            text-align: center; font-size: 8pt; border-top: 1px solid #000; padding-top: 4px;
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#7c3aed; color:white;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#7c3aed; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
        🖨️ Cetak / Print
    </button>
    <button onclick="window.close(); if(!window.closed) { window.history.back(); }" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">
        ← Kembali
    </button>
</div>

@php
    $kopBase64 = null;
    if ($settings && $settings->logo_path) {
        $imgPath = storage_path('app/public/' . $settings->logo_path);
        if (file_exists($imgPath)) {
            $mime = mime_content_type($imgPath);
            $kopBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgPath));
        }
    }

    $nomorST      = $program->nomor_st ?? '2.14/2191/LP.00.04/IV/2026';
    $tanggalST    = $program->tanggal_sk ?? '17 April 2026';
    $pj           = $program->programInstructors->where('is_penanggung_jawab', true)->first();
    $namaPJ       = $pj?->instructor->name ?? 'Shaidul Maula, S.Tr.T.';
@endphp

<div class="page">

    @if($kopBase64)
        <div class="kop-wrap">
            <img src="{{ $kopBase64 }}" alt="Kop Surat">
        </div>
    @endif

    <div class="judul-dok">
        <h2>SURAT PENUGASAN INSTRUKTUR</h2>
    </div>
    <div class="nomor-st">Nomor {{ $nomorST }}</div>

    <p>Kepala Balai Pelatihan Vokasi dan Produktivitas Banda Aceh dengan ini menginstruksikan Saudara/i yang tersebut namanya di bawah ini untuk melaksanakan Pelatihan Durasi Pendek Batch {{ $program->angkatan ?? 'IV' }} Kejuruan TIK Program <strong>{{ $program->masterProgram->name ?? 'AI for Automation I' }}</strong> selama <strong>{{ $program->jp ?? '40' }} JP</strong>.</p>
    <p>Dimulai tanggal <strong>{{ $program->start_date?->format('d F Y') ?? '20 April 2026' }}</strong> sampai dengan <strong>{{ $program->end_date?->format('d F Y') ?? '23 April 2026' }}</strong> bertempat di BPVP Banda Aceh dengan menunjuk Saudara/i <strong>{{ $namaPJ }}</strong> sebagai Instruktur dan Wali Kelas Program {{ $program->masterProgram->name ?? 'AI for Automation I' }}.</p>

    <table class="tabel-unit">
        <thead>
            <tr>
                <th>NO.</th>
                <th>NAMA INSTRUKTUR</th>
                <th>INSTRUKTUR PENGGANTI</th>
                <th>JUDUL UNIT KOMPETENSI</th>
                <th>DURASI</th>
            </tr>
        </thead>
        <tbody>
            @php $units = $program->selected_units_with_details ?? collect([]); @endphp
            @forelse($units as $idx => $unit)
            <tr>
                <td style="text-align:center;">{{ $idx + 1 }}.</td>
                <td>{{ $idx === 0 ? $namaPJ : '' }}</td>
                <td></td>
                <td><span class="bullet-check">{{ $unit['unit']->name ?? '-' }}</span></td>
                <td style="text-align:center;">{{ $unit['custom_duration'] ?? 0 }} JP</td>
            </tr>
            @empty
            <tr>
                <td>1.</td>
                <td>{{ $namaPJ }}</td>
                <td></td>
                <td>-</td>
                <td style="text-align:center;">40 JP</td>
            </tr>
            @endforelse
            <tr style="font-weight:bold; background:#f9f9f9;">
                <td colspan="4" style="text-align:right;">JUMLAH</td>
                <td style="text-align:center;">{{ $program->jp ?? '40' }} JP</td>
            </tr>
        </tbody>
    </table>

    <p>Demikian Instruksi ini harap dilaksanakan sebagaimana mestinya.</p>

    <div class="ttd-area">
        <p>{{ $settings->tempat_surat ?? 'Banda Aceh' }}, {{ $tanggalST }}</p>
        <p>Kepala Balai,</p>
        <br><br><br>
        <p style="font-weight:bold; text-decoration:underline;">{{ $settings->nama_pengirim ?? 'Rahmad Faisal' }}</p>
        <p>NIP {{ $settings->nip_pengirim ?? '198103302009011005' }}</p>
    </div>

</div>

<div class="footer-elektronik">
    Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik<br>
    yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).
</div>

</body>
</html>
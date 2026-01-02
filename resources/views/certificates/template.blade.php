<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SERTIFIKAT KELULUSAN PELATIHAN - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            font-size: 10pt;
            line-height: 1.6;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: #fff url('{{ asset("images/border-blue-microtext.png") }}') no-repeat center center;
            background-size: cover;
            overflow: hidden;
        }

        /* LOGO POSISI */
        .logo-kiri { 
            position: absolute; 
            top: 8mm; 
            left: 12mm; 
            width: 25mm;
            height: 25mm;
        }
        .logo-kanan { 
            position: absolute; 
            top: 8mm; 
            right: 12mm; 
            width: 30mm;
            height: 25mm;
        }
        .logo-bawah { 
            position: absolute; 
            bottom: 12mm; 
            left: 12mm; 
            width: 32mm;
            height: 32mm;
        }

        /* HEADER */
        .header {
            position: absolute;
            top: 10mm;
            left: 45mm;
            right: 50mm;
            text-align: center;
            font-size: 8pt;
            line-height: 1.4;
        }
        .header-line1, .header-line2 {
            font-weight: normal;
            margin-bottom: 1mm;
        }
        .header-line3 {
            font-weight: bold;
            font-size: 9pt;
        }

        /* JUDUL */
        .judul {
            position: absolute;
            top: 40mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 18pt;
            font-weight: normal;
            letter-spacing: 2px;
        }

        /* NOMOR */
        .nomor {
            position: absolute;
            top: 52mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
        }

        /* PERNYATAAN */
        .pernyataan {
            position: absolute;
            top: 62mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10pt;
        }

        /* DATA FIELDS */
        .data-fields {
            position: absolute;
            top: 72mm;
            left: 60mm;
            font-size: 10pt;
            line-height: 1.8;
        }
        .field-row {
            margin: 2mm 0;
        }
        .field-label {
            display: inline-block;
            width: 55mm;
        }
        .field-value {
            display: inline;
        }
        .nama-value {
            font-weight: bold;
            font-size: 11pt;
        }

        /* DESKRIPSI */
        .deskripsi {
            position: absolute;
            top: 107mm;
            left: 50mm;
            right: 50mm;
            text-align: center;
            font-size: 10pt;
            line-height: 1.6;
        }
        .program-name {
            font-weight: bold;
            margin: 2mm 0;
        }
        .lulus-text {
            font-weight: bold;
            margin-top: 2mm;
        }

        /* TEMPAT TANGGAL */
        .tempat-tanggal {
            position: absolute;
            top: 140mm;
            right: 60mm;
            width: 60mm;
            text-align: center;
            font-size: 10pt;
            line-height: 1.6;
        }

        /* TTD */
        .ttd-kepala {
            position: absolute;
            bottom: 25mm;
            right: 60mm;
            width: 60mm;
            text-align: center;
        }
        .ttd-name {
            margin-top: 15mm;
            font-size: 10pt;
        }
        .ttd-nip {
            font-size: 9pt;
            margin-top: 1mm;
        }

        /* QR CODE */
        .qr-code {
            position: absolute;
            bottom: 30mm;
            right: 17mm;
            width: 22mm;
            height: 22mm;
        }
        .qr-code img {
            width: 100%;
            height: 100%;
        }

        /* FOOTER */
        .footer-text {
            position: absolute;
            bottom: 8mm;
            left: 50mm;
            right: 50mm;
            text-align: center;
            font-size: 6.5pt;
            line-height: 1.3;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="certificate">

        <!-- Logo Kiri Atas -->
        <div class="logo-kiri">
            <img src="{{ asset('images/logo blk banda aceh.png') }}" alt="Logo BPVP">
        </div>

        <!-- Logo Kanan Atas -->
        <div class="logo-kanan">
            <img src="{{ asset('images/logo-kemnaker.png') }}" alt="Logo Kemnaker">
        </div>

        <!-- Logo Emas Kiri Bawah -->
        <div class="logo-bawah">
            <img src="{{ asset('images/logo-kuning.png') }}" alt="Logo Emas">
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-line1">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
            <div class="header-line2">DIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
            <div class="header-line3">BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH</div>
        </div>

        <!-- Judul -->
        <div class="judul">SERTIFIKAT KELULUSAN PELATIHAN</div>

        <!-- Nomor -->
        <div class="nomor">NOMOR: {{ $certificate->certificate_number }}</div>

        <!-- Pernyataan -->
        <div class="pernyataan">Dengan ini menyatakan bahwa:</div>

        <!-- Data Fields -->
        <div class="data-fields">
            <div class="field-row">
                <span class="field-label">Nama</span>
                <span class="field-value">: <span class="nama-value">{{ strtoupper($certificate->participant->name ?? '-') }}</span></span>
            </div>
            <div class="field-row">
                <span class="field-label">NIK</span>
                <span class="field-value">: {{ $certificate->participant->nik ?? '-' }}</span>
            </div>
            <div class="field-row">
                <span class="field-label">Tempat, tanggal lahir</span>
                <span class="field-value">
                    : {{ strtoupper($certificate->participant->birth_place ?? '-') }}, 
                    {{ $certificate->participant->birth_date ? \Carbon\Carbon::parse($certificate->participant->birth_date)->isoFormat('DD MMMM YYYY') : '-' }}
                </span>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="deskripsi">
            telah menyelesaikan Pelatihan Berbasis Kompetensi (PBK) Non Boarding Bidang Industri dan Jasa untuk program<br>
            <span class="program-name">{{ strtoupper($certificate->program->masterProgram->name ?? 'N/A') }}</span> yang dilaksanakan pada<br>
            tanggal {{ $certificate->program->start_date ? \Carbon\Carbon::parse($certificate->program->start_date)->isoFormat('DD MMMM YYYY') : '27 Oktober 2025' }} 
            sampai dengan {{ $certificate->program->end_date ? \Carbon\Carbon::parse($certificate->program->end_date)->isoFormat('DD MMMM YYYY') : '01 Desember 2025' }} 
            selama {{ $certificate->program->duration ?? '260' }} Jam Pelatihan dan dinyatakan <span class="lulus-text">LULUS</span>.
        </div>

        <!-- Tempat Tanggal -->
        <div class="tempat-tanggal">
            Banda Aceh, {{ \Carbon\Carbon::parse($certificate->issue_date)->isoFormat('DD MMMM YYYY') }}<br>
            Kepala Balai Pelatihan Vokasi<br>
            dan Produktivitas Banda Aceh,
        </div>

        <!-- Tanda Tangan -->
        <div class="ttd-kepala">
            <div class="ttd-name">Rahmad Faisal</div>
            <div class="ttd-nip">NIP 19810330 200901 1 005</div>
        </div>

        <!-- QR Code -->
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('admin.certificates.certificate.verify', $certificate->certificate_number)) }}" 
                 alt="QR Code">
        </div>

        <!-- Footer -->
        <div class="footer-text">
            Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik<br>
            yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).
        </div>

    </div>
</body>
</html>
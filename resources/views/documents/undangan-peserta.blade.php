<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Undangan Peserta - {{ $program->masterProgram->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin-top: 20px; }
        .footer { margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BALAI LATIHAN KERJA BANDA ACEH</h2>
        <p>Jl. Contoh No. 123, Banda Aceh</p>
        <p>Telp: (0651) 123456 | Email: info@blkbandaaceh.go.id</p>
    </div>

    <h3 style="text-align: center; text-decoration: underline;">SURAT UNDANGAN PESERTA PELATIHAN</h3>

    <div class="content">
        <p>Kepada Yth.</p>
        <p><strong>{{ $program->participant->name ?? 'Peserta Terpilih' }}</strong><br>
           NIK: {{ $program->participant->nik ?? '-' }}<br>
           Alamat: {{ $program->participant->address ?? '-' }}</p>

        <p>Dengan hormat,</p>
        <p>Kami mengundang Bapak/Ibu/Saudara untuk mengikuti Pelatihan <strong>{{ $program->masterProgram->name }}</strong> dengan detail sebagai berikut:</p>

        <ul style="margin-left: 20px;">
            <li><strong>Program:</strong> {{ $program->masterProgram->name }} ({{ $program->angkatan }})</li>
            <li><strong>Jadwal:</strong> {{ $program->start_date->format('d F Y') }} s/d {{ $program->end_date->format('d F Y') }}</li>
            <li><strong>Lokasi:</strong> BLK Banda Aceh (Ruang {{ $program->room ?? 'Belum ditentukan' }})</li>
            <li><strong>Waktu:</strong> 08.00 – 16.00 WIB</li>
        </ul>

        <p>Mohon kehadiran Bapak/Ibu tepat waktu. Bawa KTP dan perlengkapan pribadi sesuai kebutuhan pelatihan.</p>

        <p>Terima kasih atas perhatian dan partisipasinya.</p>

        <p>Banda Aceh, {{ $today }}</p>
        <p>Kepala BLK Banda Aceh</p>
        <br><br><br>
        <p>(Nama Kepala)</p>
    </div>
</body>
</html>
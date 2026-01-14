<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Tugas Instruktur - {{ $program->masterProgram->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SURAT TUGAS INSTRUKTUR</h2>
        <p>Nomor: [Nomor Surat Otomatis]</p>
    </div>

    <p>Dengan ini kami menugaskan:</p>
    <p><strong>{{ $program->instructor->name ?? 'Nama Instruktur' }}</strong><br>
       NIP/NIK: {{ $program->instructor->nik ?? '-' }}</p>

    <p>Untuk menjadi Instruktur pada pelatihan:</p>
    <ul>
        <li><strong>Program:</strong> {{ $program->masterProgram->name }} ({{ $program->angkatan }})</li>
        <li><strong>Jadwal:</strong> {{ $program->start_date->format('d F Y') }} s/d {{ $program->end_date->format('d F Y') }}</li>
        <li><strong>Lokasi:</strong> BLK Banda Aceh</li>
    </ul>

    <p>Demikian surat tugas ini dibuat untuk dilaksanakan dengan penuh tanggung jawab.</p>

    <p>Banda Aceh, {{ $today }}</p>
    <p>Kepala BLK Banda Aceh</p>
    <br><br><br>
    <p>(Nama Kepala)</p>
</body>
</html>
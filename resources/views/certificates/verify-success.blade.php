<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Sertifikat</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <h1 class="text-3xl font-bold text-green-600 mb-4">Sertifikat Valid!</h1>
        <p class="text-lg mb-6">Nomor: <strong>{{ $certificate->certificate_number }}</strong></p>
        <p class="mb-4">Penerima: <strong>{{ $certificate->participant->name }}</strong></p>
        <p class="mb-4">Program: <strong>{{ $certificate->program->masterProgram->name ?? 'N/A' }}</strong></p>
        <p class="mb-4">Tanggal Terbit: <strong>{{ $certificate->issue_date->format('d F Y') }}</strong></p>
        <p class="text-green-600 font-semibold">Diterbitkan oleh BPVP Banda Aceh</p>
    </div>
</body>
</html>
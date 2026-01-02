<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Sertifikat</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <h1 class="text-3xl font-bold text-red-600 mb-4">Sertifikat Tidak Valid</h1>
        <p class="text-lg mb-6">{{ $message }}</p>
        <p class="text-gray-600">Hubungi BPVP Banda Aceh untuk informasi lebih lanjut.</p>
    </div>
</body>
</html>
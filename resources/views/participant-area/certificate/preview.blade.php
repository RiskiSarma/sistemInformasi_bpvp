@extends('layouts.participant')

@section('title', 'Preview Sertifikat')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Preview Sertifikat</h2>
        <a href="{{ route('participant.certificate.index') }}" class="text-blue-600 hover:underline">
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow border overflow-hidden">
        <iframe src="{{ route('participant.certificate.download', $certificate) }}#toolbar=0&view=FitH" 
                class="w-full border-0" 
                style="height: 80vh;">
            Browser Anda tidak mendukung preview PDF. <a href="{{ route('participant.certificate.download', $certificate) }}">Unduh di sini</a>.
        </iframe>
    </div>

    <div class="flex justify-center gap-4 mt-6">
        <a href="{{ route('participant.certificate.download', $certificate) }}?preview=1" target="_blank"
        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
            Lihat di Tab Baru
        </a>
        <a href="{{ route('participant.certificate.download', $certificate) }}"
        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            Unduh Sertifikat
        </a>
    </div>
</div>
@endsection
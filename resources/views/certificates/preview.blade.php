@extends('layouts.app')

@section('title', 'Preview Sertifikat')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Preview Sertifikat</h2>
            <p class="text-gray-600 mt-1">{{ $certificate->certificate_number }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.certificates.view', $certificate->id) }}" 
               target="_blank"
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Full Screen
            </a>
            <a href="{{ route('admin.certificates.download', $certificate->id) }}" 
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </a>
            <a href="{{ route('admin.certificates.index') }}" 
               class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Certificate Info -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm text-gray-600 font-medium">Nama Peserta</label>
                <p class="text-gray-900 font-semibold">{{ $participant->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600 font-medium">Program</label>
                <p class="text-gray-900 font-semibold">{{ $program->masterProgram->name }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600 font-medium">Batch</label>
                <p class="text-gray-900 font-semibold">{{ $program->batch }}</p>
            </div>
            <div>
                <label class="text-sm text-gray-600 font-medium">Status</label>
                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-semibold">
                    {{ ucfirst($certificate->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Certificate Preview -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <iframe src="{{ route('admin.certificates.view', $certificate->id) }}" 
                class="w-full border-0" 
                style="height: 85vh;">
        </iframe>
    </div>
</div>
@endsection
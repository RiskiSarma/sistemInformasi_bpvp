@extends('layouts.app')

@section('title', 'Detail Sertifikat')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.certificates.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Kembali ke Daftar Sertifikat</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-teal-600 to-teal-800 px-6 py-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Detail Sertifikat</h2>
                    <p class="text-teal-100">{{ $certificate->certificate_number }}</p>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        {{ $certificate->status === 'issued' ? 'bg-green-500' : 
                           ($certificate->status === 'draft' ? 'bg-yellow-500' : 'bg-red-500') }}">
                        {{ ucfirst($certificate->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6">
            <!-- Certificate Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Informasi Peserta</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $certificate->participant->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <p class="text-sm text-gray-700">{{ $certificate->participant->email }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Telepon</p>
                                <p class="text-sm text-gray-700">{{ $certificate->participant->phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Status Peserta</p>
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($certificate->participant->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Informasi Program</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Program Pelatihan</p>
                                <p class="text-lg font-semibold text-gray-800">{{ $certificate->program->masterProgram->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Batch</p>
                                <p class="text-sm text-gray-700">{{ $certificate->program->batch ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Periode</p>
                                <p class="text-sm text-gray-700">
                                    {{ $certificate->program->start_date ? $certificate->program->start_date->format('d M Y') : '-' }}
                                    s.d.
                                    {{ $certificate->program->end_date ? $certificate->program->end_date->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificate Details -->
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Detail Sertifikat</h3>
                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Nomor Sertifikat</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $certificate->certificate_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Tanggal Terbit</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $certificate->issue_date->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $certificate->status === 'issued' ? 'bg-green-100 text-green-800' : 
                               ($certificate->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($certificate->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Dibuat Pada</p>
                        <p class="text-sm text-gray-700">{{ $certificate->created_at->format('d F Y H:i') }}</p>
                    </div>
                </div>
            </div>

            @if($certificate->notes)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Catatan</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-gray-700">{{ $certificate->notes }}</p>
                </div>
            </div>
            @endif

            <!-- PDF Preview -->
            @if($certificate->pdf_path)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Preview Sertifikat</h3>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-center space-x-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800">PDF Sertifikat Tersedia</p>
                            <p class="text-sm text-gray-600">File PDF telah di-generate</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.certificates.preview', $certificate) }}" target="_blank" class="px-4 py-2 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Preview PDF</span>
                </a>
                <a href="{{ route('admin.certificates.download', $certificate) }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Download PDF</span>
                </a>
            </div>
            <form action="{{ route('admin.certificates.destroy', $certificate) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
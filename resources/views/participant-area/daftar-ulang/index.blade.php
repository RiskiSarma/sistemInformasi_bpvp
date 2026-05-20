@extends('layouts.participant')

@section('title', 'Daftar Ulang')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-teal-600 to-blue-700 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Daftar Ulang</h1>
                <p class="text-blue-100 mt-1">Upload berkas persyaratan pendaftaran ulang Anda</p>
            </div>
            <div class="hidden md:flex flex-col items-end">
                <span class="text-blue-100 text-sm mb-1">Progress Upload</span>
                <div class="flex items-center space-x-3">
                    <div class="w-40 h-3 bg-blue-500 rounded-full overflow-hidden">
                        <div class="h-3 bg-white rounded-full transition-all duration-500"
                             style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <span class="font-bold text-lg">{{ $progressPercent }}%</span>
                </div>
                <span class="text-blue-100 text-xs mt-1">{{ $totalUploaded }} dari {{ $totalRequired }} dokumen</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-3xl font-bold text-gray-800">{{ $totalRequired }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Dokumen</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-3xl font-bold text-yellow-500">{{ $totalUploaded }}</p>
            <p class="text-sm text-gray-500 mt-1">Sudah Diupload</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-3xl font-bold text-green-500">{{ $totalApproved }}</p>
            <p class="text-sm text-gray-500 mt-1">Disetujui</p>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start space-x-3">
        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-700">
            <p class="font-semibold mb-1">Ketentuan Upload Berkas:</p>
            <ul class="list-disc list-inside space-y-1 text-teal-600">
                <li>Format file yang diterima: <strong>JPG, PNG, PDF</strong></li>
                <li>Ukuran maksimal per file: <strong>5 MB</strong></li>
                <li>Pastikan file terbaca dengan jelas</li>
                <li>Dokumen yang sudah disetujui tidak dapat dihapus</li>
            </ul>
        </div>
    </div>

    {{-- Daftar Dokumen --}}
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h2 class="font-semibold text-gray-800">Daftar Dokumen Persyaratan</h2>
        </div>

        <div class="divide-y">
            @foreach($requiredDocuments as $type => $label)
                @php
                    $doc = $uploadedDocuments->get($type);
                    $isUploaded = !is_null($doc);
                    $isApproved = $isUploaded && $doc->status === 'approved';
                    $isRejected = $isUploaded && $doc->status === 'rejected';
                    $isPending  = $isUploaded && $doc->status === 'pending';
                @endphp

                <div class="p-5" x-data="{ showUpload: {{ $isRejected || !$isUploaded ? 'true' : 'false' }} }">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        {{-- Info Dokumen --}}
                        <div class="flex items-start space-x-4">
                            {{-- Icon / Status --}}
                            <div class="flex-shrink-0 mt-1">
                                @if($isApproved)
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                @elseif($isRejected)
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                @elseif($isPending)
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <p class="font-semibold text-gray-800">{{ $label }}</p>
                                @if($isUploaded)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $doc->file_name }} &bull; {{ $doc->file_size }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $doc->status_badge }}">
                                        {{ $doc->status_label }}
                                    </span>
                                    @if($isRejected && $doc->catatan)
                                        <p class="text-xs text-red-600 mt-1">
                                            <strong>Catatan:</strong> {{ $doc->catatan }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-xs text-gray-400 mt-0.5">Belum diupload</p>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center space-x-2 flex-shrink-0">
                            @if($isUploaded)
                                {{-- Preview --}}
                                <a href="{{ route('participant.daftar-ulang.preview', $doc->id) }}"
                                   target="_blank"
                                   class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat
                                </a>

                                {{-- Ganti / Upload Ulang --}}
                                @if(!$isApproved)
                                    <button @click="showUpload = !showUpload"
                                            class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-50 hover:bg-blue-100 text-teal-600 rounded-lg transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        Ganti
                                    </button>

                                    {{-- Hapus --}}
                                    <form action="{{ route('participant.daftar-ulang.destroy', $doc->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Hapus dokumen ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-sm bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- Upload Pertama Kali --}}
                                <button @click="showUpload = !showUpload"
                                        class="inline-flex items-center px-4 py-2 text-sm bg-teal-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    Upload
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Form Upload (toggle) --}}
                    @if(!$isApproved)
                        <div x-show="showUpload" x-transition class="mt-4">
                            <form action="{{ route('participant.daftar-ulang.upload') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  class="bg-gray-50 rounded-xl p-4 border border-dashed border-gray-300">
                                @csrf
                                <input type="hidden" name="document_type" value="{{ $type }}">

                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih File <span class="text-red-500">*</span>
                                    <span class="font-normal text-gray-400">(JPG, PNG, PDF — maks. 5 MB)</span>
                                </label>

                                <div class="flex items-center space-x-3">
                                    <input type="file"
                                           name="file"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           required
                                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-teal-600 hover:file:bg-blue-100">

                                    <button type="submit"
                                            class="flex-shrink-0 px-4 py-2 bg-teal-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                        Simpan
                                    </button>
                                    <button type="button" @click="showUpload = false"
                                            class="flex-shrink-0 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-lg transition">
                                        Batal
                                    </button>
                                </div>

                                @error('file')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Semua selesai --}}
    @if($totalApproved === $totalRequired)
        <div class="bg-green-50 border border-green-200 rounded-xl p-5 flex items-center space-x-4">
            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-green-800 text-lg">Daftar Ulang Selesai!</p>
                <p class="text-green-600 text-sm">Semua dokumen telah diverifikasi. Selamat, pendaftaran ulang Anda berhasil.</p>
            </div>
        </div>
    @endif

</div>
@endsection
@extends('layouts.app')

@section('title', 'Pengaturan Dokumen - ' . ucwords(str_replace('-', ' ', $template)))

@php
    $templateMeta = [
        'sk-peserta' => [
            'label' => 'SPT Peserta',
            'icon'  => '📋',
            'color' => 'blue',
            'desc'  => 'Surat Perintah Tugas Peserta Pelatihan',
        ],
        'sk-penyelenggara' => [
            'label' => 'SK Penyelenggara',
            'icon'  => '📜',
            'color' => 'rose',
            'desc'  => 'Surat Keputusan Penyelenggaraan Pelatihan',
        ],
        'st-instruktur' => [
            'label' => 'ST Instruktur',
            'icon'  => '👨‍🏫',
            'color' => 'purple',
            'desc'  => 'Surat Tugas Instruktur',
        ],
        // ... tambahkan yang lain jika perlu
    ];

    $meta = $templateMeta[$template] ?? ['label' => ucwords(str_replace('-', ' ', $template)), 'icon' => '📄', 'color' => 'gray', 'desc' => ''];
    $c = [
        'blue'  => ['bg' => 'bg-blue-600',   'light' => 'bg-blue-50',   'text' => 'text-blue-700', 'ring' => 'focus:ring-blue-500'],
        'rose'  => ['bg' => 'bg-rose-600',   'light' => 'bg-rose-50',   'text' => 'text-rose-700', 'ring' => 'focus:ring-rose-500'],
        'purple'=> ['bg' => 'bg-purple-600', 'light' => 'bg-purple-50', 'text' => 'text-purple-700','ring' => 'focus:ring-purple-500'],
        'gray'  => ['bg' => 'bg-gray-600',   'light' => 'bg-gray-50',   'text' => 'text-gray-700', 'ring' => 'focus:ring-gray-500'],
    ][$meta['color']];
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.programs.show', $program) }}" 
               class="p-3 rounded-xl border hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-3xl">{{ $meta['icon'] }}</span>
                    <h1 class="text-3xl font-bold text-gray-900">Pengaturan {{ $meta['label'] }}</h1>
                </div>
                <p class="text-gray-600 mt-1">{{ $meta['desc'] }}</p>
                <p class="text-sm text-gray-500">{{ $settings->name }} — {{ $program->masterProgram->name ?? '-' }}</p>
            </div>
        </div>

        <a href="{{ route('admin.programs.dokumen.' . $template, $program) }}" target="_blank"
           class="flex items-center gap-2 px-6 py-3 {{ $c['bg'] }} text-white rounded-2xl hover:opacity-90 transition font-medium shadow-sm">
            👁️ Preview Dokumen
        </a>
    </div>

    {{-- FORM --}}
    <form method="POST" 
          action="{{ route('admin.programs.dokumen.update-template', [$program->id, $template]) }}"
          enctype="multipart/form-data"
          class="space-y-8">

        @csrf
        @method('PUT')

        {{-- 1. KOP SURAT & LOGO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center gap-3">
                🏛️ Kop Surat & Logo Lembaga
            </h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teks Kop Surat</label>
                    <textarea name="kop_surat" rows="6" 
                              class="w-full px-5 py-4 border border-gray-300 rounded-2xl font-mono text-sm focus:ring-2 {{ $c['ring'] }} focus:border-transparent resize-y">
                        {{ old('kop_surat', $settings->kop_surat) }}
                    </textarea>
                    <p class="text-xs text-gray-400 mt-2">Setiap baris akan menjadi baris baru pada kop surat.</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Logo Lembaga</label>
                    <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-2xl p-8 bg-gray-50 hover:bg-gray-100 transition">
                        @if($settings->logo_path)
                            <img src="{{ Storage::url($settings->logo_path) }}" 
                                 class="h-24 object-contain mb-4" alt="Logo">
                            <p class="text-xs text-gray-500">Logo saat ini</p>
                        @else
                            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center shadow-inner">
                                <span class="text-4xl text-gray-300">🖼️</span>
                            </div>
                        @endif
                        
                        <input type="file" name="logo" accept="image/png,image/jpeg" 
                               class="mt-6 text-sm text-gray-600 file:mr-4 file:py-2 file:px-6 file:rounded-xl file:border-0 file:bg-gray-200 file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-3 text-center">PNG / JPG • Maks 2 MB</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. DASAR HUKUM (hanya sk-peserta) --}}
        @if($template === 'sk-peserta')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">⚖️ Dasar Hukum</h3>
            <div class="space-y-6">
                @for($i = 1; $i <= 5; $i++)
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-2xl {{ $c['bg'] }} text-white text-sm font-bold flex items-center justify-center mt-1">
                        {{ $i }}
                    </div>
                    <textarea name="dasar_hukum_{{ $i }}" rows="3"
                              class="flex-1 px-5 py-4 border border-gray-300 rounded-2xl focus:ring-2 {{ $c['ring'] }}">
                        {{ old("dasar_hukum_{$i}", $settings->{"dasar_hukum_{$i}"}) }}
                    </textarea>
                </div>
                @endfor
            </div>
        </div>
        @endif

        {{-- 3. FORMAT SURAT & TEMPAT --}}
        @if(in_array($template, ['sk-peserta', 'sk-penyelenggara', 'st-instruktur']))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">🔢 Format Penomoran & Lokasi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Nomor Surat</label>
                    <input type="text" name="format_nomor" 
                           value="{{ old('format_nomor', $settings->format_nomor) }}"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl font-mono text-sm focus:ring-2 {{ $c['ring'] }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota / Tempat Surat</label>
                    <input type="text" name="tempat_surat" 
                           value="{{ old('tempat_surat', $settings->tempat_surat) }}"
                           placeholder="Banda Aceh"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl focus:ring-2 {{ $c['ring'] }}">
                </div>
            </div>
        </div>
        @endif

        {{-- 4. TANDA TANGAN --}}
        @if(in_array($template, ['sk-peserta', 'sk-penyelenggara', 'st-instruktur']))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6">✍️ Data Tanda Tangan (Srikandi / TTE)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Variabel TTD Pengirim</label>
                    <input type="text" name="ttd_pengirim" 
                           value="{{ old('ttd_pengirim', $settings->ttd_pengirim) }}"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pejabat</label>
                    <input type="text" name="nama_pengirim" 
                           value="{{ old('nama_pengirim', $settings->nama_pengirim) }}"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIP Pejabat</label>
                    <input type="text" name="nip_pengirim" 
                           value="{{ old('nip_pengirim', $settings->nip_pengirim) }}"
                           class="w-full px-5 py-4 border border-gray-300 rounded-2xl font-mono">
                </div>
            </div>
        </div>
        @endif

        {{-- TOMBOL AKSI --}}
        <div class="flex justify-between pt-4">
            <a href="{{ route('admin.programs.show', $program) }}" 
               class="px-8 py-4 border border-gray-300 text-gray-700 rounded-2xl hover:bg-gray-50 transition font-medium">
                ← Kembali ke Program
            </a>
            
            <button type="submit" 
                    class="px-10 py-4 {{ $c['bg'] }} text-white rounded-2xl hover:opacity-90 transition font-semibold shadow-sm flex items-center gap-2">
                💾 Simpan Semua Pengaturan
            </button>
        </div>

    </form>
</div>
@endsection
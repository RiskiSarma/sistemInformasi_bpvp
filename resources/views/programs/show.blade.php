@extends('layouts.app')

@section('title', 'Detail Pelatihan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.programs.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Program Pelatihan</h2>
                <p class="text-gray-600 mt-1">{{ $program->display_name }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.edit', $program) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Program
            </a>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- SECTION GENERATE DOKUMEN ADMINISTRASI -->
    <!-- ============================================================ -->
    <!-- Generate Dokumen Administrasi -->
<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600">
        <h3 class="text-white font-semibold text-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Generate Dokumen Administrasi
        </h3>
        <p class="text-emerald-100 text-sm mt-1">SK, ST, Absensi, Jadwal & dokumen lainnya</p>
    </div>
 
    <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
 
            {{-- SPT Peserta --}}
            <div class="border rounded-lg p-4 hover:border-blue-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">SPT Peserta</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Surat Perintah Tugas Peserta</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.sk-peserta', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'sk-peserta']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
            {{-- SK Penyelenggara --}}
            <div class="border rounded-lg p-4 hover:border-rose-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">SK Penyelenggara</h4>
                        <p class="text-xs text-gray-500 mt-0.5">SK Penyelenggaraan Pelatihan</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.sk-penyelenggara', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-rose-600 text-white text-sm rounded hover:bg-rose-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'sk-penyelenggara']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
            {{-- ST Instruktur --}}
            <div class="border rounded-lg p-4 hover:border-purple-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">ST Instruktur</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Surat Tugas Instruktur</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.st-instruktur', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'st-instruktur']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
            {{-- Jadwal --}}
            <div class="border rounded-lg p-4 hover:border-orange-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">Jadwal Pelatihan</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Jadwal Kegiatan & Unit</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.jadwal', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'jadwal']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
            {{-- Daftar Hadir --}}
            <div class="border rounded-lg p-4 hover:border-teal-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">Daftar Hadir</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Lembar Absensi Peserta</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.daftar-hadir', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-teal-600 text-white text-sm rounded hover:bg-teal-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'daftar-hadir']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
            {{-- Biodata Peserta --}}
            <div class="border rounded-lg p-4 hover:border-indigo-400 hover:shadow-sm transition">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">Biodata Peserta</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Rekap Data Peserta</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.programs.dokumen.biodata-peserta', $program) }}"
                       target="_blank"
                       class="flex-1 text-center px-3 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700 transition">
                        📄 Generate
                    </a>
                    <a href="{{ route('admin.programs.dokumen.edit-template', [$program->id, 'biodata-peserta']) }}"
                       class="px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded hover:bg-gray-200 transition"
                       title="Edit Template">
                        ✏️
                    </a>
                </div>
            </div>
 
        </div>
    </div>
</div>
    <!-- END SECTION GENERATE DOKUMEN -->

    <!-- Main Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Program -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Informasi Program</span>
            </h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Master Program</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $program->masterProgram->name ?? 'N/A' }}</dd>
                    <dd class="text-sm text-gray-500">Kode: {{ $program->masterProgram->code ?? '-' }}</dd>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis Pelatihan</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full bg-purple-100 text-purple-800">
                                {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Angkatan</dt>
                        <dd class="mt-1">
                            @if($program->angkatan)
                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800">
                                Angkatan {{ $program->angkatan }}
                            </span>
                            @else
                            <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-600">Belum di-set</span>
                            @endif
                        </dd>
                    </div>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Paket Pelatihan</dt>
                    <dd class="mt-1 text-gray-900">
                        @if($program->paketPelatihan)
                            {{ $program->paketPelatihan->tahun }} - Batch {{ $program->paketPelatihan->batch }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Periode</dt>
                    <dd class="mt-1 text-gray-900">
                        {{ $program->start_date->format('d F Y') }} s/d {{ $program->end_date->format('d F Y') }}
                        <span class="text-sm text-gray-500">({{ $program->start_date->diffInDays($program->end_date) }} hari)</span>
                    </dd>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kuota</dt>
                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ $program->participants->count() }} / {{ $program->max_participants ?? '∞' }}
                        </dd>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Ada Industri</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full {{ $program->ada_industri === 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $program->ada_industri === 'Y' ? 'Ya' : 'Tidak' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">JP Harian</dt>
                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ $program->jp_harian ? $program->jp_harian . ' jam' : '-' }}
                        </dd>
                    </div>
                </div>
            </dl>
        </div>

        <!-- Info Audit -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Informasi Audit</span>
            </h3>
            <dl class="space-y-6">
                <div class="bg-gray-50 px-5 py-4 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-2 text-lg font-semibold text-gray-900">{{ $program->creator?->name ?? 'Sistem' }}</dd>
                    <dd class="text-sm text-gray-500">{{ $program->created_at->format('d F Y, H:i') }}</dd>
                </div>
                <div class="bg-gray-50 px-5 py-4 rounded-lg">
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                    <dd class="mt-2 text-lg font-semibold text-gray-900">{{ $program->updater?->name ?? '-' }}</dd>
                    <dd class="text-sm text-gray-500">{{ $program->updated_at->format('d F Y, H:i') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Unit Kompetensi -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Unit Kompetensi</span>
                </h3>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Total JP</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $program->total_jp_from_selected_units }} jam</div>
                </div>
            </div>
        </div>
        <div class="p-6">
            @php
                $unitsData = $program->selected_units_with_details;
                $groupedByType = $unitsData->groupBy('type');
            @endphp
            @if($unitsData->count() > 0)
                @foreach(['reguler' => 'Reguler', 'softskill' => 'Softskill', 'skkni' => 'SKKNI', 'industri' => 'Industri'] as $type => $label)
                    @if($groupedByType->has($type))
                    <div class="mb-6 last:mb-0">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center space-x-2">
                            <span class="w-3 h-3 rounded-full {{ $type === 'reguler' ? 'bg-blue-500' : ($type === 'softskill' ? 'bg-purple-500' : ($type === 'skkni' ? 'bg-green-500' : 'bg-orange-500')) }}"></span>
                            <span>{{ $label }}</span>
                            <span class="text-sm text-gray-500">({{ $groupedByType[$type]->sum('custom_duration') }} JP)</span>
                        </h4>
                        <div class="space-y-2">
                            @foreach($groupedByType[$type] as $item)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border hover:border-blue-300 transition">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $item['unit']->code ?? '-' }}</div>
                                    <div class="text-sm text-gray-600">{{ $item['unit']->name ?? '-' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold text-blue-600">{{ $item['custom_duration'] }}</div>
                                    <div class="text-xs text-gray-500">JP</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
            <div class="text-center py-8 text-gray-500">
                <p>Belum ada unit kompetensi terdaftar</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Instruktur -->
<div class="bg-white rounded-lg shadow-sm border">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Instruktur Pengajar ({{ $program->programInstructors->count() }})</span>
        </h3>
    </div>

    <div class="p-6">
        @if($program->programInstructors->isEmpty())
            <p class="text-gray-500 italic text-center py-8">
                Belum ada instruktur ditugaskan.
            </p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                @foreach($program->programInstructors as $pi)

    @php
        // cek apakah instruktur internal atau eksternal
        $isInternal = !is_null($pi->instructor);

        // ambil data
        if ($isInternal) {
            $instructorName = $pi->instructor->name ?? 'Nama tidak ditemukan';
            $instructorEmail = $pi->instructor->email ?? '-';
        } else {
            $instructorName = $pi->pengajarEksternal->nama ?? 'Nama tidak ditemukan';
            $instructorEmail = $pi->pengajarEksternal->email ?? '-';
        }
    @endphp

    <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-lg border hover:border-blue-300 transition">

        <!-- Avatar -->
        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
            {{ strtoupper(substr($instructorName, 0, 1)) }}
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">

            <!-- Nama -->
            <div class="font-medium text-gray-800 truncate">
                {{ $instructorName }}
            </div>

            <!-- Badge -->
            <div class="flex items-center gap-2 mt-1">

                @if($isInternal)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Internal
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        Eksternal
                    </span>
                @endif

                @if($pi->is_penanggung_jawab)
                        <span class="px-3 py-1 text-xs font-semibold bg-green-600 text-white rounded-full">
                            Penanggung Jawab
                        </span>
                    @endif
            </div>

            <!-- Email -->
            <div class="text-xs text-gray-500 mt-1 truncate">
                {{ $instructorEmail }}
            </div>

        </div>
    </div>

@endforeach
            </div>
        @endif
    </div>
</div>

        {{-- Peserta --}}
@if($program->participants->count() > 0)
<div class="bg-white rounded-lg shadow-sm border" x-data="{ 
    deleteParticipantModalOpen: false, 
    selectedParticipantId: null,
    selectedParticipantName: ''
}">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Daftar Peserta ({{ $program->participants->count() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">No</th>
                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">NIK</th>
                    <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Status</th>
                    <th class="px-6 py-3 text-center text-xs uppercase text-gray-500 w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($program->participants as $key => $participant)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $key + 1 }}</td>
                    <td class="px-6 py-4">{{ $participant->user->name ?? $participant->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $participant->nik ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($participant->status ?? 'pending') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            {{-- Tombol Edit → ke halaman edit peserta --}}
                            <a href="{{ route('admin.participants.edit', $participant) }}"
                               class="inline-flex items-center px-2.5 py-1.5 bg-blue-50 text-blue-700 text-xs font-medium rounded hover:bg-blue-100 transition"
                               title="Edit Peserta">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            {{-- Tombol Remove dari Program --}}
                            <button
                                @click="deleteParticipantModalOpen = true; selectedParticipantId = '{{ $participant->id }}'; selectedParticipantName = '{{ addslashes($participant->user->name ?? $participant->name ?? 'peserta ini') }}'"
                                class="inline-flex items-center px-2.5 py-1.5 bg-red-50 text-red-700 text-xs font-medium rounded hover:bg-red-100 transition"
                                title="Lepas dari Program">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6h12a6 6 0 00-6-6zM21 12H15"/>
                                </svg>
                                Lepas
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- MODAL: Lepas Peserta dari Program --}}
    <div x-show="deleteParticipantModalOpen" style="display:none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div @click="deleteParticipantModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full relative">
                <form :action="`{{ url('admin/programs/' . $program->id . '/participants') }}/${selectedParticipantId}/remove`" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6h12a6 6 0 00-6-6zM21 12H15"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Lepas Peserta dari Program</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Peserta <strong x-text="selectedParticipantName"></strong> akan dilepas dari program ini.
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex gap-2">
                                <svg class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-xs text-blue-700">Data peserta <strong>tidak akan dihapus</strong> dari sistem. Peserta hanya dilepas dari program ini dan tetap terdaftar di menu Peserta.</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <button type="button" @click="deleteParticipantModalOpen = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-100 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6h12a6 6 0 00-6-6zM21 12H15"/>
                            </svg>
                            Ya, Lepas dari Program
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

    {{-- ============================================================ --}}
{{-- TAMBAHKAN SECTION INI DI programs/show.blade.php             --}}
{{-- LOKASI: SETELAH SECTION "Peserta" (paling bawah)             --}}
{{-- ============================================================ --}}

<!-- Paket Units & Sub-Units -->
<div class="bg-white rounded-lg shadow-sm border" x-data="{
    activeTab: 'units',
    addUnitModalOpen: false,
    deleteUnitModalOpen: false,
    addSubUnitModalOpen: false,
    deleteSubUnitModalOpen: false,
    selectedUnitId: null,
    selectedSubUnitId: null
}">
    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <div class="flex space-x-8 px-6">
            <button 
                @click="activeTab = 'units'" 
                :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'units', 'text-gray-500 hover:text-gray-700': activeTab !== 'units'}" 
                class="py-4 px-1 text-sm font-medium transition">
                Paket Units
            </button>
            <button 
                @click="activeTab = 'sub-units'" 
                :class="{'border-b-2 border-blue-500 text-blue-600': activeTab === 'sub-units', 'text-gray-500 hover:text-gray-700': activeTab !== 'sub-units'}" 
                class="py-4 px-1 text-sm font-medium transition">
                Paket Sub-Units
            </button>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        
        <!-- TAB: PAKET UNITS -->
        <div x-show="activeTab === 'units'" class="space-y-4">
            <!-- Button Tambah -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Paket Units</h3>
                <button 
                    @click="addUnitModalOpen = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Unit
                </button>
            </div>

            <!-- Table -->
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Program Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Master Sub Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">JP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Sub Unit Kompetensi</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($program->paketPelatihanUnits as $index => $unit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">
                                    {{ $unit->programPelatihanUnit->independentCompetencyUnit->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $unit->programPelatihanUnit->independentCompetencyUnit->code ?? '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $unit->masterProgramSubUnit->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $unit->jp ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full {{ $unit->sub_unit_kompetensi == 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $unit->sub_unit_kompetensi == 'Y' ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button 
                                    @click="deleteUnitModalOpen = true; selectedUnitId = '{{ $unit->id }}'"
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-sm">Belum ada unit. Klik "Tambah Unit" untuk menambahkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB: PAKET SUB-UNITS -->
        <div x-show="activeTab === 'sub-units'" class="space-y-4">
            <!-- Button Tambah -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Paket Sub-Units</h3>
                <button 
                    @click="addSubUnitModalOpen = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Sub-Unit
                </button>
            </div>

            <!-- Table -->
            <div class="border rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Paket Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Master Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Unit Kompetensi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">JP</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $allSubUnits = $program->paketPelatihanUnits->flatMap(fn($unit) => $unit->paketPelatihanSubUnits);
                        @endphp
                        @forelse($allSubUnits as $index => $subUnit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $subUnit->paketPelatihanUnit->programPelatihanUnit->independentCompetencyUnit->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $subUnit->masterProgram->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="font-medium">
                                    {{ $subUnit->unitKompetensi->name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $subUnit->unitKompetensi->code ?? '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $subUnit->jp ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <button 
                                    @click="deleteSubUnitModalOpen = true; selectedSubUnitId = '{{ $subUnit->id }}'"
                                    class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <p class="text-sm">Belum ada sub-unit. Klik "Tambah Sub-Unit" untuk menambahkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL ADD UNIT -->
    <div x-show="addUnitModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div @click="addUnitModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full">
                <form method="POST" action="{{ route('admin.programs.paket-units.store', $program->id) }}">
                    @csrf
                    <div class="bg-white px-6 py-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tambah Paket Unit</h3>
                            <button type="button" @click="addUnitModalOpen = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Program Unit <span class="text-red-500">*</span></label>
                                <select name="program_pelatihan_unit_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Program Unit --</option>
                                    @foreach($programPelatihanUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->independentCompetencyUnit->name ?? 'Unit #' . $unit->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Master Sub Unit <span class="text-red-500">*</span></label>
                                <select name="master_program_sub_unit_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Sub Unit --</option>
                                    @foreach($masterPrograms as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">JP (Jam Pelajaran)</label>
                                <input type="number" name="jp" min="0" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Unit Kompetensi? <span class="text-red-500">*</span></label>
                                <select name="sub_unit_kompetensi" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="N">Tidak</option>
                                    <option value="Y">Ya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <button type="button" @click="addUnitModalOpen = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE UNIT -->
    <div x-show="deleteUnitModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div @click="deleteUnitModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form :action="`{{ route('admin.programs.show', $program->id) }}/paket-units/${selectedUnitId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="bg-white px-6 py-4">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Hapus Paket Unit</h3>
                            </div>
                        </div>
                        
                        <!-- ✅ TAMBAHKAN PERINGATAN INI -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Peringatan:</strong> Menghapus unit ini akan <strong>otomatis menghapus semua sub-units</strong> yang terkait. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500">Yakin ingin menghapus unit ini beserta semua sub-units terkait?</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <button type="button" @click="deleteUnitModalOpen = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Ya, Hapus Semua
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL ADD SUB-UNIT -->
    <div x-show="addSubUnitModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div @click="addSubUnitModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-2xl sm:w-full">
                <form method="POST" action="{{ route('admin.programs.paket-sub-units.store', $program->id) }}">
                    @csrf
                    <div class="bg-white px-6 py-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Tambah Paket Sub-Unit</h3>
                            <button type="button" @click="addSubUnitModalOpen = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket Unit <span class="text-red-500">*</span></label>
                                <select name="paket_pelatihan_unit_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Paket Unit --</option>
                                    @foreach($program->paketPelatihanUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->programPelatihanUnit->independentCompetencyUnit->name ?? 'Unit #' . $unit->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Master Program <span class="text-red-500">*</span></label>
                                <select name="master_programs_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Master Program --</option>
                                    @foreach($masterPrograms as $mp)
                                        <option value="{{ $mp->id }}">{{ $mp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kompetensi <span class="text-red-500">*</span></label>
                                <select name="independent_competency_units" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Unit Kompetensi --</option>
                                    @foreach($allCompetencyUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name ?? $unit->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">JP (Jam Pelajaran)</label>
                                <input type="number" name="jp" min="0" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <button type="button" @click="addSubUnitModalOpen = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE SUB-UNIT -->
    <div x-show="deleteSubUnitModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
            <div @click="deleteSubUnitModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="inline-block bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form :action="`{{ route('admin.programs.show', $program->id) }}/paket-sub-units/${selectedSubUnitId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="bg-white px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-900">Hapus Paket Sub-Unit</h3>
                        <p class="mt-2 text-sm text-gray-500">Yakin ingin menghapus sub-unit ini?</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                        <button type="button" @click="deleteSubUnitModalOpen = false" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
</div>
@endsection
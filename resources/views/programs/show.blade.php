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
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden" x-data="{ openDok: true }">
        <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 flex items-center justify-between cursor-pointer"
             @click="openDok = !openDok">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-lg">Generate Dokumen Administrasi</h3>
                    <p class="text-emerald-100 text-sm">SK, ST, Absensi, Jadwal & dokumen lainnya</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-white transition-transform duration-200" :class="openDok ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>

        <div x-show="openDok" x-transition>
            <div class="p-6">

                <!-- Info peringatan jika data belum lengkap -->
                @php
                    $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first();
                    $warningDocs = [];
                    if (!$pj) $warningDocs[] = 'Penanggung Jawab belum ditentukan';
                    if ($program->participants->count() === 0) $warningDocs[] = 'Belum ada peserta terdaftar';
                @endphp
                @if(count($warningDocs) > 0)
                <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-lg flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-semibold mb-1">Perhatian — beberapa dokumen mungkin tidak lengkap:</p>
                        <ul class="list-disc ml-4 space-y-0.5">
                            @foreach($warningDocs as $w)<li>{{ $w }}</li>@endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Grid Dokumen -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    {{-- SK Peserta --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">SPT Peserta</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Surat Perintah Tugas Peserta</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.sk-peserta', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                    {{-- ST Instruktur --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 transition">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">ST Instruktur</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Surat Tugas instruktur pengajar</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.st-instruktur', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-purple-600 text-white text-xs rounded-lg hover:bg-purple-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                    {{-- Jadwal Pelatihan --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-orange-200 transition">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">Jadwal Pelatihan</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Jadwal kegiatan & unit kompetensi</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.jadwal', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-orange-600 text-white text-xs rounded-lg hover:bg-orange-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                    {{-- Daftar Hadir --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-teal-200 transition">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">Daftar Hadir</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Lembar absensi peserta harian</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.daftar-hadir', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-teal-600 text-white text-xs rounded-lg hover:bg-teal-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                    {{-- Biodata Peserta --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 transition">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">Biodata Peserta</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Rekap data peserta pelatihan</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.biodata-peserta', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                    {{-- SK Penyelenggara --}}
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-emerald-400 hover:shadow-sm transition group">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-rose-200 transition">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm">SK Penyelenggara</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Surat Keputusan Penyelenggaraan pelatihan</p>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('admin.programs.dokumen.sk-penyelenggara', $program) }}"
                               target="_blank"
                               class="flex-1 text-center px-3 py-1.5 bg-rose-600 text-white text-xs rounded-lg hover:bg-rose-700 transition font-medium">
                                📄 Generate
                            </a>
                        </div>
                    </div>

                </div><!-- end grid -->

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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Instruktur Pengajar ({{ $program->programInstructors->count() }})</span>
            </h3>
        </div>
        <div class="p-6">
            @if($program->programInstructors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($program->programInstructors as $pi)
                <div class="p-4 border rounded-lg {{ $pi->is_penanggung_jawab ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <div class="font-semibold text-gray-900">{{ $pi->instructor->name ?? '-' }}</div>
                                @if($pi->is_penanggung_jawab)
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-600 text-white">Penanggung Jawab</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 mt-1">{{ $pi->instructor->email ?? $pi->instructor->phone ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-500"><p>Belum ada instruktur ditugaskan</p></div>
            @endif
        </div>
    </div>

    <!-- Peserta -->
    @if($program->participants->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border">
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
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($program->participants as $key => $participant)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $key + 1 }}</td>
                        <td class="px-6 py-4">{{ $participant->name ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $participant->nik ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($participant->status ?? 'pending') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
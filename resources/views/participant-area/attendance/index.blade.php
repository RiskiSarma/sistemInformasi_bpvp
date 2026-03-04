@extends('layouts.participant')

@section('title', 'Kehadiran Saya')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Kehadiran Saya</h2>
        <p class="text-gray-500 mt-1 text-sm">Rekap kehadiran Anda di program pelatihan</p>
    </div>

    @php
        $minAttendance  = 80; // syarat minimum kehadiran (%)
        $totalAll       = $attendances->total();
        $totalHadir     = $attendances->getCollection()->where('status', 'present')->count();
        $totalAbsen     = $attendances->getCollection()->where('status', 'absent')->count();
        $totalTerlambat = $attendances->getCollection()->where('status', 'late')->count();
        $totalIzin      = $attendances->getCollection()->where('status', 'excused')->count();
        $pctHadir       = $totalAll > 0 ? round(($totalHadir / $totalAll) * 100) : 0;
        $lulus          = $pctHadir >= $minAttendance;
        $maxAbsen       = (int) floor($totalAll * (1 - $minAttendance / 100));
        $sisaBolehAbsen = max(0, $maxAbsen - $totalAbsen);
    @endphp

    <!-- Status Kelulusan Kehadiran — Card Utama -->
    <div class="rounded-xl border-2 shadow-sm overflow-hidden
        {{ $lulus ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
        <div class="px-6 py-5 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0
                    {{ $lulus ? 'bg-green-100' : 'bg-red-100' }}">
                    @if($lulus)
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @else
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    @endif
                </div>
                <div>
                    <p class="text-lg font-bold {{ $lulus ? 'text-green-800' : 'text-red-800' }}">
                        {{ $lulus ? 'Memenuhi Syarat Kehadiran' : 'Belum Memenuhi Syarat Kehadiran' }}
                    </p>
                    <p class="text-sm {{ $lulus ? 'text-green-600' : 'text-red-600' }} mt-0.5">
                        @if($totalAll === 0)
                            Belum ada data kehadiran yang tercatat
                        @elseif($lulus)
                            Kehadiran Anda <strong>{{ $pctHadir }}%</strong> — sudah memenuhi minimum {{ $minAttendance }}%
                        @else
                            Kehadiran Anda <strong>{{ $pctHadir }}%</strong> — belum mencapai minimum {{ $minAttendance }}%
                        @endif
                    </p>
                </div>
            </div>
            <div class="text-right flex-shrink-0 ml-4">
                <div class="text-4xl font-black {{ $lulus ? 'text-green-700' : 'text-red-700' }}">
                    {{ $pctHadir }}%
                </div>
                <div class="text-xs {{ $lulus ? 'text-green-500' : 'text-red-500' }} mt-0.5">
                    min. {{ $minAttendance }}%
                </div>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="px-6 pb-5">
            <div class="relative w-full bg-white rounded-full h-3 border {{ $lulus ? 'border-green-200' : 'border-red-200' }}">
                <!-- Garis batas minimum -->
                <div class="absolute top-0 bottom-0 w-0.5 bg-gray-400 rounded-full z-10"
                     style="left: {{ $minAttendance }}%"></div>
                <!-- Bar kehadiran -->
                <div class="h-full rounded-full transition-all {{ $lulus ? 'bg-green-500' : 'bg-red-500' }}"
                     style="width: {{ min($pctHadir, 100) }}%"></div>
            </div>
            <div class="flex justify-between text-xs mt-1.5">
                <span class="{{ $lulus ? 'text-green-600' : 'text-red-600' }} font-medium">
                    {{ $pctHadir }}% kehadiran Anda
                </span>
                <span class="text-gray-400">Batas minimum {{ $minAttendance }}%</span>
            </div>
        </div>
    </div>

    <!-- Summary 4 Kolom -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Hadir</p>
                <span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalHadir }}</p>
            <p class="text-xs text-gray-400 mt-0.5">dari {{ $totalAll }} pertemuan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Tidak Hadir</p>
                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalAbsen }}</p>
            @if($totalAll > 0)
            <p class="text-xs mt-0.5 {{ $sisaBolehAbsen <= 0 ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                @if($sisaBolehAbsen > 0)
                    maks. boleh absen {{ $maxAbsen }}×
                @else
                    batas absen terlampaui
                @endif
            </p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Terlambat</p>
                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalTerlambat }}</p>
            <p class="text-xs text-gray-400 mt-0.5">pertemuan</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Izin</p>
                <span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalIzin }}</p>
            <p class="text-xs text-gray-400 mt-0.5">pertemuan</p>
        </div>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Riwayat Kehadiran</h3>
                <p class="text-xs text-gray-400 mt-0.5">Urutan terbaru di atas</p>
            </div>
            <span class="text-xs text-gray-400">{{ $attendances->total() }} pertemuan tercatat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Program</th>
                        <th class="px-6 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attendances as $attendance)
                    @php
                        $cfg = match($attendance->status) {
                            'present' => ['label' => 'Hadir',       'dot' => 'bg-green-500',  'badge' => 'bg-green-100 text-green-800'],
                            'absent'  => ['label' => 'Tidak Hadir', 'dot' => 'bg-red-500',    'badge' => 'bg-red-100 text-red-800'],
                            'late'    => ['label' => 'Terlambat',   'dot' => 'bg-orange-500', 'badge' => 'bg-orange-100 text-orange-800'],
                            'excused' => ['label' => 'Izin',        'dot' => 'bg-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-800'],
                            default   => ['label' => ucfirst($attendance->status), 'dot' => 'bg-gray-400', 'badge' => 'bg-gray-100 text-gray-600'],
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $attendance->date->locale('id')->translatedFormat('d F Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $attendance->date->locale('id')->translatedFormat('l') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $attendance->program->masterProgram->name ?? $attendance->program->name ?? '-' }}
                            </div>
                            @if(isset($attendance->program->batch))
                            <div class="text-xs text-gray-400">{{ $attendance->program->batch }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $cfg['badge'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                <span>{{ $cfg['label'] }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $attendance->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-400 font-medium text-sm">Belum ada data kehadiran</p>
                            <p class="text-xs text-gray-400 mt-1">Data akan muncul setelah instruktur mencatat kehadiran</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
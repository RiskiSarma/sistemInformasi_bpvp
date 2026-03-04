@extends('layouts.app')

@section('title', 'Rekap Kehadiran')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Rekap Kehadiran</h2>
            <p class="text-gray-500 mt-1 text-sm">Statistik kehadiran peserta per program</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}"
           class="inline-flex items-center text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit"
                    class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                    Filter
                </button>
                @if(request('date_from') || request('date_to'))
                <a href="{{ route('admin.attendance.recap') }}"
                   class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    @php $minAttendance = 80; @endphp

    <!-- Recap per Program -->
    @forelse($programs as $program)
    @php
        $stats          = $program->attendance_stats;
        $totalPeserta   = $stats->count();
        $lulusCount     = $stats->filter(fn($s) => $s['percentage'] >= $minAttendance)->count();
        $atRiskCount    = $totalPeserta - $lulusCount;
        $avgPct         = $totalPeserta > 0 ? round($stats->avg('percentage'), 1) : 0;
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- Program Header -->
        <div class="px-6 py-5 border-b bg-gray-50">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900">
                        {{ $program->masterProgram->name ?? 'N/A' }}
                        <span class="text-gray-400 font-normal">— {{ $program->batch }}</span>
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $program->start_date->format('d M Y') }} – {{ $program->end_date->format('d M Y') }}
                    </p>
                </div>
                <!-- Summary syarat kehadiran -->
                <div class="flex items-center gap-3">
                    <div class="text-center px-4 py-2 bg-white border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-400 mb-0.5">Rata-rata</p>
                        <p class="text-xl font-bold {{ $avgPct >= $minAttendance ? 'text-green-600' : 'text-red-600' }}">
                            {{ $avgPct }}%
                        </p>
                    </div>
                    <div class="text-center px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-xs text-green-600 mb-0.5">Memenuhi syarat</p>
                        <p class="text-xl font-bold text-green-700">{{ $lulusCount }}<span class="text-sm font-normal text-green-500">/{{ $totalPeserta }}</span></p>
                    </div>
                    @if($atRiskCount > 0)
                    <div class="text-center px-4 py-2 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs text-red-600 mb-0.5">Perlu perhatian</p>
                        <p class="text-xl font-bold text-red-700">{{ $atRiskCount }}<span class="text-sm font-normal text-red-500">/{{ $totalPeserta }}</span></p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left w-8">#</th>
                        <th class="px-6 py-3 text-left">Peserta</th>
                        <th class="px-6 py-3 text-center">Total</th>
                        <th class="px-6 py-3 text-center text-green-600">Hadir</th>
                        <th class="px-6 py-3 text-center text-red-600">Absen</th>
                        <th class="px-6 py-3 text-center text-orange-600">Terlambat</th>
                        <th class="px-6 py-3 text-center text-yellow-600">Izin</th>
                        <th class="px-6 py-3 text-center">Kehadiran</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($stats->sortBy('percentage') as $i => $stat)
                    @php
                        $lulus   = $stat['percentage'] >= $minAttendance;
                        $maxAbsen = (int) floor($stat['total'] * (1 - $minAttendance / 100));
                        $sisaAbsen = max(0, $maxAbsen - $stat['absent']);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ !$lulus ? 'bg-red-50/30' : '' }}">
                        <td class="px-6 py-4 text-xs text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $lulus ? 'bg-green-100' : 'bg-red-100' }}">
                                    <span class="text-xs font-bold {{ $lulus ? 'text-green-700' : 'text-red-700' }}">
                                        {{ strtoupper(substr($stat['participant']->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $stat['participant']->name }}</p>
                                    @if($stat['participant']->nik)
                                    <p class="text-xs text-gray-400">{{ $stat['participant']->nik }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 font-medium">{{ $stat['total'] }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-green-700">{{ $stat['present'] }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-red-700">{{ $stat['absent'] }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-orange-700">{{ $stat['late'] }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-yellow-700">{{ $stat['excused'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center space-y-1">
                                <span class="font-bold text-sm {{ $lulus ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $stat['percentage'] }}%
                                </span>
                                <div class="w-16 bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $lulus ? 'bg-green-500' : 'bg-red-500' }}"
                                         style="width: {{ min($stat['percentage'], 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($lulus)
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Memenuhi syarat</span>
                            </span>
                            @else
                            <div class="flex flex-col items-center space-y-1">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Belum memenuhi</span>
                                </span>
                                @if($stat['total'] > 0 && $sisaAbsen >= 0)
                                <span class="text-xs text-red-500">
                                    kurang {{ $minAttendance - $stat['percentage'] }}% lagi
                                </span>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer info -->
        <div class="px-6 py-3 bg-gray-50 border-t flex items-center justify-between text-xs text-gray-400">
            <span>Syarat minimum kehadiran: <strong class="text-gray-600">{{ $minAttendance }}%</strong></span>
            <span>Diurutkan dari kehadiran terendah</span>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-dashed border-gray-300 p-16 text-center">
        <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <p class="text-gray-400 font-medium">Tidak ada data kehadiran</p>
        <p class="text-xs text-gray-400 mt-1">Coba ubah filter tanggal atau pastikan absensi sudah dicatat</p>
    </div>
    @endforelse
</div>
@endsection
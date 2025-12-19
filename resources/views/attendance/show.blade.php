@extends('layouts.app')

@section('title', 'Kehadiran - ' . ($program->masterProgram->name ?? 'Program'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.attendance.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        <a href="{{ route('admin.attendance.recap') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
            Lihat Rekap Keseluruhan
        </a>
    </div>

    <!-- Program Info -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $program->masterProgram->name ?? 'N/A' }}</h2>
        <p class="text-gray-600">{{ $program->batch }} • {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}</p>
        <p class="text-gray-600 mt-2">Total Peserta: {{ $program->participants->count() }} orang</p>
        <td class="px-6 py-4 text-sm text-gray-600">
            Dicatat oleh: {{ $attendance->recorder?->name ?? '-' }}
        </td>
    </div>

    <!-- Info Penting untuk Admin -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-blue-800">
                <strong>Catatan:</strong> Admin hanya dapat memantau kehadiran. Pencatatan absen harian hanya dapat dilakukan oleh instruktur pengampu.
            </p>
        </div>
    </div>

    <!-- Riwayat Kehadiran (Sama seperti sebelumnya) -->
    @if($dates->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Kehadiran</h3>
        
        <div class="space-y-4">
            @foreach($dates as $date)
            @php
                $dateKey = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : $date;
                $dayAttendances = $attendances[$dateKey] ?? collect();
                $presentCount = $dayAttendances->where('status', 'present')->count();
                $absentCount = $dayAttendances->where('status', 'absent')->count();
                $excusedCount = $dayAttendances->where('status', 'excused')->count();
                $lateCount = $dayAttendances->where('status', 'late')->count();
            @endphp
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    </div>
                    <div class="text-sm text-gray-500">
                        Total tercatat: {{ $dayAttendances->count() }} peserta
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="text-green-600">
                        <span class="font-semibold text-lg">{{ $presentCount }}</span><br>Hadir
                    </div>
                    <div class="text-red-600">
                        <span class="font-semibold text-lg">{{ $absentCount }}</span><br>Tidak Hadir
                    </div>
                    <div class="text-yellow-600">
                        <span class="font-semibold text-lg">{{ $excusedCount }}</span><br>Izin
                    </div>
                    <div class="text-orange-600">
                        <span class="font-semibold text-lg">{{ $lateCount }}</span><br>Terlambat
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-500">Belum ada riwayat kehadiran untuk program ini</p>
        <p class="text-sm text-gray-400 mt-2">Instruktur belum melakukan pencatatan absen</p>
    </div>
    @endif
</div>
@endsection
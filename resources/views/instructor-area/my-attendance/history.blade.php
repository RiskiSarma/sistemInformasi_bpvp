@extends('layouts.instructor')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('instructor.my-attendance.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Riwayat Absensi</h2>
                <p class="text-gray-600 mt-1">Lihat riwayat kehadiran mengajar Anda</p>
            </div>
        </div>
        
        <!-- Month Selector -->
        <form method="GET" action="{{ route('instructor.my-attendance.history') }}">
            <input type="month" name="month" value="{{ $month }}" 
                   onchange="this.form.submit()"
                   class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-green-50 p-4 rounded-lg shadow-sm border border-green-200">
            <div class="text-sm text-green-600">Hadir</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_present'] }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm border border-yellow-200">
            <div class="text-sm text-yellow-600">Terlambat</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['total_late'] }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg shadow-sm border border-red-200">
            <div class="text-sm text-red-600">Tidak Hadir</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['total_absent'] }}</div>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm border border-blue-200">
            <div class="text-sm text-blue-600">Izin</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_permission'] }}</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg shadow-sm border border-purple-200">
            <div class="text-sm text-purple-600">Sakit</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_sick'] }}</div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-6 border-b bg-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">
                Riwayat Bulan {{ \Carbon\Carbon::parse($month)->locale('id')->isoFormat('MMMM Y') }}
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keluar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $attendance->date->format('d/m/Y') }}
                            <div class="text-xs text-gray-500">{{ $attendance->date->locale('id')->isoFormat('dddd') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">
                                {{ $attendance->schedule->program->masterProgram->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $attendance->schedule->program->batch }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($attendance->schedule->start_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($attendance->schedule->end_time)->format('H:i') }}
                            @if($attendance->schedule->room)
                                <div class="text-xs text-gray-500">{{ $attendance->schedule->room }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($attendance->check_in)
                                <span class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($attendance->check_out)
                                <span class="font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $attendance->duration ? number_format($attendance->duration / 60, 1) . ' jam' : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 text-xs rounded-full {{ $attendance->status_badge }}">
                                {{ $attendance->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $attendance->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-lg font-semibold mb-2">Belum Ada Data</p>
                            <p>Tidak ada riwayat absensi pada bulan ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
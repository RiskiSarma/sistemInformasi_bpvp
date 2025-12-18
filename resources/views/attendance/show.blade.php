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
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Program Info -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $program->masterProgram->name ?? 'N/A' }}</h2>
        <p class="text-gray-600">{{ $program->batch }} • {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}</p>
        <p class="text-gray-600 mt-2">Total Peserta: {{ $program->participants->count() }} orang</p>
    </div>

    <!-- Attendance Form -->
    <div class="bg-white rounded-lg shadow-sm border p-6" x-data="{ selectedDate: '{{ date('Y-m-d') }}' }">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Catat Kehadiran</h3>
        
        <form method="POST" action="{{ route('admin.attendance.record') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            
            <div class="flex items-center space-x-4 mb-6">
                <label for="date" class="text-sm font-medium text-gray-700">Tanggal:</label>
                <input type="date" name="date" id="date" x-model="selectedDate" value="{{ date('Y-m-d') }}" required class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tidak Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Izin</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($program->participants as $index => $participant)
                        <tr>
                            <td class="px-4 py-3">
                                <input type="hidden" name="attendances[{{ $index }}][participant_id]" value="{{ $participant->id }}">
                                <div class="font-medium text-gray-900">{{ $participant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $participant->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="attendances[{{ $index }}][status]" value="present" required class="w-4 h-4 text-blue-600">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="attendances[{{ $index }}][status]" value="absent" class="w-4 h-4 text-red-600">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="attendances[{{ $index }}][status]" value="excused" class="w-4 h-4 text-yellow-600">
                            </td>
                            <td class="px-4 py-3 text-center">
                                <input type="radio" name="attendances[{{ $index }}][status]" value="late" class="w-4 h-4 text-orange-600">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" name="attendances[{{ $index }}][notes]" placeholder="Catatan..." class="w-full px-2 py-1 text-sm border rounded">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance History -->
    @if($dates->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Kehadiran</h3>
        
        <div class="space-y-4">
            @foreach($dates as $date)
            @php
                // Format date key untuk matching dengan array
                $dateKey = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : $date;
                $dayAttendances = $attendances[$dateKey] ?? collect();
                $presentCount = $dayAttendances->where('status', 'present')->count();
                $absentCount = $dayAttendances->where('status', 'absent')->count();
                $excusedCount = $dayAttendances->where('status', 'excused')->count();
                $lateCount = $dayAttendances->where('status', 'late')->count();
            @endphp
            <div class="border rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($date)->format('d F Y') }}
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: {{ $dayAttendances->count() }} peserta
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="text-green-600">
                        <span class="font-semibold">{{ $presentCount }}</span> Hadir
                    </div>
                    <div class="text-red-600">
                        <span class="font-semibold">{{ $absentCount }}</span> Tidak Hadir
                    </div>
                    <div class="text-yellow-600">
                        <span class="font-semibold">{{ $excusedCount }}</span> Izin
                    </div>
                    <div class="text-orange-600">
                        <span class="font-semibold">{{ $lateCount }}</span> Terlambat
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
        <p class="text-gray-500">Belum ada riwayat kehadiran</p>
    </div>
    @endif
</div>
@endsection 
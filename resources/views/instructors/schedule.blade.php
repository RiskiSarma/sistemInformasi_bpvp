@extends('layouts.app')

@section('title', 'Jadwal Instruktur')

@section('content')
<div class="space-y-6">
    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.instructors.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Jadwal Instruktur</h2>
                <p class="text-gray-600 mt-1">{{ $instructor->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.schedules.create', $instructor) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Jam Mengajar</span>
        </a>
    </div>

    <!-- Instructor Info Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start space-x-6">
            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-3xl font-bold text-purple-600">{{ substr($instructor->name, 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-800">{{ $instructor->name }}</h3>
                <p class="text-purple-600 mt-1">{{ $instructor->expertise }}</p>
                <div class="mt-3 flex items-center space-x-6 text-sm text-gray-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $instructor->email }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $instructor->weekly_teaching_hours }} jam/minggu</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm rounded-full {{ $instructor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $instructor->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Table -->
    @php
        $schedules = $instructor->schedules()->with('program.masterProgram')->active()->ordered()->get();
        $groupedSchedules = $schedules->groupBy('day_of_week');
        $days = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'];
    @endphp

    @if($schedules->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-6 border-b bg-blue-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800">Jadwal Mengajar Mingguan</h3>
                </div>
                <div class="text-sm text-gray-600">
                    Total: {{ $instructor->weekly_teaching_hours }} jam per minggu
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Hari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ruangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-40">Dibuat/Oleh</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($days as $dayKey => $dayName)
                        @if($groupedSchedules->has($dayKey))
                            @foreach($groupedSchedules[$dayKey] as $index => $schedule)
<tr class="hover:bg-gray-50">
    @if($index === 0)
    <td class="px-6 py-4 align-top font-medium text-gray-900" rowspan="{{ $groupedSchedules[$dayKey]->count() }}">
        {{ $dayName }}
    </td>
    @endif

    <td class="px-6 py-4 text-sm text-gray-900">
        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
    </td>
    <td class="px-6 py-4">
        <div class="font-medium text-gray-900">{{ $schedule->program->masterProgram->name ?? 'N/A' }}</div>
        <div class="text-xs text-gray-500">{{ $schedule->program->batch }}</div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">
        {{ $schedule->room ?? '-' }}
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">
        {{ $schedule->notes ?? '-' }}
    </td>

    <!-- KOLOM BARU: Dibuat/Oleh - hanya di baris pertama tiap hari -->
    @if($index === 0)
    <td class="px-6 py-4 text-xs text-gray-600 align-top" rowspan="{{ $groupedSchedules[$dayKey]->count() }}">
        <div class="font-medium">Dibuat: {{ $schedule->creator?->name ?? 'Sistem' }}</div>
        <div class="text-gray-500 text-xs">{{ $schedule->created_at->format('d/m/Y H:i') }}</div>
        
        @if($schedule->updater && $schedule->updated_at->gt($schedule->created_at))
        <div class="font-medium mt-2">Update: {{ $schedule->updater?->name ?? 'Sistem' }}</div>
        <div class="text-gray-500 text-xs">{{ $schedule->updated_at->format('d/m/Y H:i') }}</div>
        @endif
    </td>
    @endif

    <!-- Kolom Aksi - tetap per baris -->
    <td class="px-6 py-4 text-sm">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2.032 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach
                        @else
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $dayName }}</td>
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-400 text-center">Tidak ada jadwal</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Jam Mengajar</h3>
        <p class="text-gray-600 mb-4">Instruktur ini belum memiliki jadwal mengajar mingguan</p>
        <a href="{{ route('admin.schedules.create', $instructor) }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Tambah Jadwal Mengajar
        </a>
    </div>
    @endif

    <!-- Statistik Program -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-sm text-gray-600">Total Program</div>
        <div class="text-2xl font-bold">{{ $totalPrograms }}</div>
    </div>
    
    <div class="bg-green-50 p-4 rounded-lg shadow">
        <div class="text-sm text-green-600">Sedang Berjalan</div>
        <div class="text-2xl font-bold text-green-600">{{ $ongoingPrograms }}</div>
    </div>
    
    <div class="bg-blue-50 p-4 rounded-lg shadow">
        <div class="text-sm text-blue-600">Akan Datang</div>
        <div class="text-2xl font-bold text-blue-600">{{ $plannedPrograms }}</div>
    </div>
    
    <div class="bg-gray-50 p-4 rounded-lg shadow">
        <div class="text-sm text-gray-600">Selesai</div>
        <div class="text-2xl font-bold">{{ $completedPrograms }}</div>
    </div>
</div>

<!-- Jadwal per Hari -->
{{-- <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">HARI</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WAKTU</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PROGRAM</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RUANGAN</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CATATAN</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">AKSI</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($days as $dayKey => $dayName)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $dayName }}</td>
                    <td colspan="5" class="px-6 py-4">
                        @if(count($schedulesByDay[$dayKey]) > 0)
                            @foreach($schedulesByDay[$dayKey] as $schedule)
                                <div class="mb-2 flex items-center justify-between">
                                    <div class="flex-1">
                                        <span class="font-medium">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                        <span class="ml-4">{{ $schedule->program->masterProgram->name ?? 'N/A' }}</span>
                                        @if($schedule->room)
                                            <span class="ml-2 text-gray-500">({{ $schedule->room }})</span>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ route('schedules.edit', $schedule) }}" class="text-blue-600">Edit</a>
                                        <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600" onclick="return confirm('Yakin hapus jadwal ini?')">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <span class="text-gray-400">Tidak ada jadwal</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> --}}
</div>
@endsection
@extends('layouts.instructor')

@section('title', 'Absensi Program')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Absensi Program</h2>
            <p class="text-gray-500 mt-1 text-sm">Pilih program untuk mencatat kehadiran peserta</p>
        </div>
        <div class="text-sm text-gray-500 bg-white border rounded-lg px-4 py-2 shadow-sm">
            {{ now()->locale('id')->translatedFormat('l, d F Y') }}
        </div>
    </div>

    <!-- Program Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($programs as $program)
        @php
            $totalParticipants = $program->participants->count();
            $todayAttendance = \App\Models\Attendance::where('program_id', $program->id)
                ->whereDate('date', today())
                ->count();
            $todayPct = $totalParticipants > 0 ? round(($todayAttendance / $totalParticipants) * 100) : 0;
            $allDone = $todayAttendance >= $totalParticipants && $totalParticipants > 0;
        @endphp
        <a href="{{ route('instructor.attendance.show', $program) }}"
           class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 overflow-hidden flex flex-col">

            <!-- Top accent bar -->
            <div class="h-1 w-full {{ $allDone ? 'bg-green-500' : 'bg-blue-500' }}"></div>

            <div class="p-5 flex-1 flex flex-col">
                <!-- Title & Badge -->
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-base leading-snug group-hover:text-blue-700 transition-colors line-clamp-2">
                            {{ $program->name }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">Batch {{ $program->batch ?? '-' }}</p>
                    </div>
                    <span class="ml-3 flex-shrink-0 px-2 py-0.5 text-xs rounded-full font-medium
                        {{ $allDone ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $allDone ? '✓ Selesai' : 'Ongoing' }}
                    </span>
                </div>

                <!-- Info -->
                <div class="space-y-1.5 text-xs text-gray-500 mb-4">
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $program->start_date->format('d M Y') }} – {{ $program->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $totalParticipants }} peserta</span>
                    </div>
                </div>

                <!-- Progress today -->
                <div class="mt-auto">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-500">Absen hari ini</span>
                        <span class="font-semibold {{ $allDone ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $todayAttendance }} / {{ $totalParticipants }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full transition-all {{ $allDone ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width: {{ $todayPct }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Footer CTA -->
            <div class="px-5 py-3 border-t bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400">
                    {{ $allDone ? 'Klik untuk lihat / update' : 'Belum semua tercatat' }}
                </span>
                <span class="text-xs font-semibold text-blue-600 group-hover:text-blue-800 flex items-center space-x-1">
                    <span>Catat Kehadiran</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </a>
        @empty
        <div class="col-span-full">
            <div class="bg-white border border-dashed border-gray-300 rounded-xl py-16 text-center">
                <svg class="w-14 h-14 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">Tidak ada program yang sedang berjalan</p>
                <p class="text-sm text-gray-400 mt-1">Program akan muncul di sini saat statusnya <em>ongoing</em></p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
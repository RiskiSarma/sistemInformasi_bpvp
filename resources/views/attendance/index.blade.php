@extends('layouts.app')

@section('title', 'Monitoring Kehadiran')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Monitoring Kehadiran</h2>
            <p class="text-gray-500 mt-1 text-sm">Pilih program untuk mengelola kehadiran peserta</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Filter status -->
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" onchange="this.form.submit()"
                    class="text-sm border border-gray-200 rounded-lg px-3 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </form>
            <a href="{{ route('admin.attendance.recap') }}"
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Rekap Kehadiran
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    @php
        $totalPrograms = $programs->count();
        $ongoingPrograms = $programs->where('status', 'ongoing')->count();
        $totalParticipants = $programs->sum(fn($p) => $p->participants->count());
    @endphp
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Program</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalPrograms }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Sedang Berjalan</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $ongoingPrograms }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Peserta</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalParticipants }}</p>
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
            $isOngoing = $program->status === 'ongoing';
        @endphp
        <a href="{{ route('admin.attendance.show', $program) }}"
           class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-200 overflow-hidden flex flex-col">

            <div class="h-1 w-full {{ $isOngoing ? ($allDone ? 'bg-green-500' : 'bg-blue-500') : 'bg-gray-300' }}"></div>

            <div class="p-5 flex-1 flex flex-col">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-base leading-snug group-hover:text-blue-700 transition-colors line-clamp-2">
                            {{ $program->masterProgram->name ?? 'N/A' }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $program->batch }}</p>
                    </div>
                    <span class="ml-3 flex-shrink-0 px-2 py-0.5 text-xs rounded-full font-medium
                        {{ $isOngoing ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>

                <div class="space-y-1.5 text-xs text-gray-500 mb-4">
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $program->start_date->format('d M') }} – {{ $program->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $totalParticipants }} peserta</span>
                    </div>
                </div>

                @if($isOngoing)
                <div class="mt-auto">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-500">Absen hari ini</span>
                        <span class="font-semibold {{ $allDone ? 'text-green-600' : 'text-blue-600' }}">
                            {{ $todayAttendance }} / {{ $totalParticipants }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $allDone ? 'bg-green-500' : 'bg-blue-500' }}"
                             style="width: {{ $todayPct }}%"></div>
                    </div>
                </div>
                @endif
            </div>

            <div class="px-5 py-3 border-t bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400">Kelola kehadiran</span>
                <span class="text-xs font-semibold text-blue-600 group-hover:text-blue-800 flex items-center space-x-1">
                    <span>Buka</span>
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
                <p class="text-gray-500 font-medium">Tidak ada program yang tersedia</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
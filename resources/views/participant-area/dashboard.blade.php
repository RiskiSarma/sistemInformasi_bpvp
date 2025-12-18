@extends('layouts.participant')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-sm p-6 text-white">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="mt-2 opacity-90">Semoga pembelajaran hari ini menyenangkan</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>
</div>

@if(isset($participant) && isset($program))
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Program Info -->
    <div class="bg-white rounded-lg shadow-sm p-6 border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Program Saya</p>
                <h3 class="text-xl font-bold text-gray-800 mt-2">{{ $program->masterProgram->name ?? 'N/A' }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $program->batch }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Attendance Percentage -->
    <div class="bg-white rounded-lg shadow-sm p-6 border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Persentase Kehadiran</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $attendancePercentage ?? 0 }}%</h3>
                <p class="text-sm mt-1 {{ ($attendancePercentage ?? 0) >= 80 ? 'text-green-600' : 'text-orange-600' }}">
                    {{ ($attendancePercentage ?? 0) >= 80 ? 'Sangat Baik!' : 'Tingkatkan kehadiran' }}
                </p>
            </div>
            <div class="w-12 h-12 {{ ($attendancePercentage ?? 0) >= 80 ? 'bg-green-100' : 'bg-orange-100' }} rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 {{ ($attendancePercentage ?? 0) >= 80 ? 'text-green-600' : 'text-orange-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Attendance -->
    <div class="bg-white rounded-lg shadow-sm p-6 border">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pertemuan</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalAttendances ?? 0 }}</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $presentCount ?? 0 }} kali hadir</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Program Details & Recent Attendance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Program Details -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Program</h3>
        <dl class="space-y-3">
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Nama Program:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $program->masterProgram->name ?? 'N/A' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Batch:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $program->batch }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Periode:</dt>
                <dd class="text-sm font-medium text-gray-900">
                    {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Status:</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Status Peserta:</dt>
                <dd>
                    <span class="px-2 py-1 text-xs rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : ($participant->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($participant->status) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Kehadiran Terbaru</h3>
            <a href="{{ route('participant.attendance') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($recentAttendances ?? [] as $attendance)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $attendance->date->format('d F Y') }}</p>
                    <p class="text-xs text-gray-600">{{ $attendance->program->masterProgram->name ?? 'N/A' }}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : ($attendance->status === 'late' ? 'bg-orange-100 text-orange-800' : ($attendance->status === 'excused' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                    {{ ucfirst($attendance->status) }}
                </span>
            </div>
            @empty
            <p class="text-sm text-gray-500 text-center py-4">Belum ada data kehadiran</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow-sm border p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('participant.program') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center group">
            <svg class="w-8 h-8 mx-auto text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-medium text-gray-800">Lihat Program</p>
        </a>
        
        <a href="{{ route('participant.attendance') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center group">
            <svg class="w-8 h-8 mx-auto text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm font-medium text-gray-800">Kehadiran</p>
        </a>
        
        <a href="{{ route('participant.program') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center group">
            <svg class="w-8 h-8 mx-auto text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="text-sm font-medium text-gray-800">Materi</p>
        </a>
        
        <a href="{{ route('participant.certificate.index') }}" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center group">
            <svg class="w-8 h-8 mx-auto text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p class="text-sm font-medium text-gray-800">Sertifikat</p>
        </a>
    </div>
</div>

@else
<!-- No Program Warning -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
    <svg class="w-16 h-16 mx-auto text-yellow-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Terdaftar di Program</h3>
    <p class="text-gray-600 mb-4">Anda belum terdaftar di program pelatihan manapun. Silakan hubungi admin untuk mendaftar.</p>
    <a href="{{ route('participant.profile.edit') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
        Lengkapi Profil
    </a>
</div>
@endif
@endsection
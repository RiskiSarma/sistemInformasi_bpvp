@extends('layouts.app')

@section('title', 'Detail Peserta')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.participants.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.participants.edit', $participant) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Data
            </a>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="flex items-start space-x-6">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
            <span class="text-3xl font-bold text-blue-600">
                {{ strtoupper(substr($participant->user->name ?? 'P', 0, 1)) }}
            </span>
        </div>
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-800">{{ $participant->user->name }}</h2>
            <p class="text-gray-600 mt-1">{{ $participant->user->email }}</p>
            <p class="text-gray-600">{{ $participant->phone ?? '-' }}</p>
            <!-- Tambahkan NIK di sini juga kalau mau -->
            @if($participant->nik)
                <p class="text-gray-600 mt-1">NIK: {{ $participant->nik }}</p>
            @endif
            <span class="mt-3 inline-block px-3 py-1 text-sm rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : ($participant->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                {{ ucfirst($participant->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Detail -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Detail</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Program</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $participant->program->masterProgram->name ?? 'N/A' }} - {{ $participant->program->batch }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">NIK</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">
                        {{ $participant->nik ?? 'Tidak diisi' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Pendidikan Terakhir</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-medium">
                        {{ $participant->education ? ucwords($participant->education) : 'Belum diisi' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $participant->address ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Daftar</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $participant->created_at->format('d F Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Attendance Stats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik Kehadiran</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600">Persentase Hadir</span>
                        <span class="text-sm font-semibold">{{ $attendancePercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $attendancePercentage }}%"></div>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Pertemuan</span>
                        <span class="font-semibold">{{ $totalAttendances }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-green-600">Hadir</span>
                        <span class="font-semibold">{{ $presentCount }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-red-600">Tidak Hadir</span>
                        <span class="font-semibold">{{ $absentCount }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-yellow-600">Terlambat</span>
                        <span class="font-semibold">{{ $lateCount }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-blue-600">Izin</span>
                        <span class="font-semibold">{{ $excusedCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
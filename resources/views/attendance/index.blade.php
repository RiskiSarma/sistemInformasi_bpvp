@extends('layouts.app')

@section('title', 'Monitoring Kehadiran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Monitoring Kehadiran</h2>
            <p class="text-gray-600 mt-1">Pilih program untuk mengelola kehadiran peserta</p>
        </div>
        <a href="{{ route('admin.attendance.recap') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            Rekap Kehadiran
        </a>
    </div>

    <!-- Programs List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
        <a href="{{ route('admin.attendance.show', $program) }}" class="block bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ ucfirst($program->status) }}
                </span>
            </div>
            
            <h3 class="font-semibold text-gray-800 text-lg mb-2">{{ $program->masterProgram->name ?? 'N/A' }}</h3>
            <p class="text-sm text-gray-600 mb-4">{{ $program->batch }}</p>
            
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center justify-between">
                    <span>Periode:</span>
                    <span class="font-medium">{{ $program->start_date->format('d M') }} - {{ $program->end_date->format('d M Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Peserta:</span>
                    <span class="font-medium">{{ $program->participants->count() }} orang</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t text-center text-blue-600 text-sm font-medium">
                Kelola Kehadiran →
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">Tidak ada program yang sedang berjalan</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
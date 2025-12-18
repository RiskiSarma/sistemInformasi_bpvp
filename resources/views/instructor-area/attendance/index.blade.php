@extends('layouts.instructor')

@section('title', 'Absensi Program')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Absensi Program</h2>
        <p class="text-gray-600 mt-1">Pilih program untuk mencatat kehadiran peserta</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
        <a href="{{ route('instructor.attendance.show', $program) }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-semibold text-gray-800">{{ $program->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Ongoing</span>
            </div>
            <p class="text-sm text-gray-600 mb-2">Batch {{ $program->batch ?? '-' }}</p>
            <p class="text-sm text-gray-600 mb-4">
                {{ $program->start_date->format('d/m/Y') }} - {{ $program->end_date->format('d/m/Y') }}
            </p>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">{{ $program->participants->count() }} peserta</span>
                <span class="text-blue-600 text-sm font-medium">Catat Kehadiran →</span>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Tidak ada program yang sedang berjalan saat ini.
        </div>
        @endforelse
    </div>
</div>
@endsection
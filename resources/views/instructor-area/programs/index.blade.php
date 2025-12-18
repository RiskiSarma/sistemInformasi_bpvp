@extends('layouts.instructor')

@section('title', 'Program Saya')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Program Saya</h2>
            <p class="text-gray-600 mt-1">Daftar program pelatihan yang Anda ajar</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode program..." class="px-4 py-2 border rounded-lg">
            <select name="status" class="px-4 py-2 border rounded-lg">
                <option value="">Semua Status</option>
                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Filter
            </button>
        </form>
    </div>

    <!-- Program Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
        <a href="{{ route('instructor.programs.show', $program) }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-semibold text-gray-800">{{ $program->name }}</h3>
                <span class="px-2 py-1 text-xs rounded-full 
                    {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : 
                       ($program->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ ucfirst($program->status) }}
                </span>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                {{ $program->start_date->format('d/m/Y') }} - {{ $program->end_date->format('d/m/Y') }}
            </p>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">{{ $program->participants->count() }} peserta</span>
                <span class="text-blue-600 font-medium">Lihat Detail →</span>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12 text-gray-500">
            Tidak ada program yang ditemukan.
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $programs->links() }}
    </div>
</div>
@endsection
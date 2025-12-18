@extends('layouts.app')

@section('title', 'Assign Program')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('instructors.show', $instructor) }}" class="text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h2 class="text-2xl font-bold">Assign Program ke {{ $instructor->name }}</h2>
    </div>

    <form action="{{ route('instructors.assign-programs.store', $instructor) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Program yang Sudah Di-assign</h3>
            @if($assignedPrograms->count() > 0)
                <div class="space-y-2">
                    @foreach($assignedPrograms as $program)
                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded">
                        <span>{{ $program->masterProgram->name ?? $program->name }} - {{ $program->batch }}</span>
                        <span class="text-green-600 text-sm">✓ Sudah di-assign</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Belum ada program yang di-assign</p>
            @endif
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Program</label>
            @if($availablePrograms->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($availablePrograms as $program)
                    <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                        <input type="checkbox" name="program_ids[]" value="{{ $program->id }}" class="mr-3">
                        <div>
                            <div class="font-medium">{{ $program->masterProgram->name ?? $program->name }}</div>
                            <div class="text-sm text-gray-600">
                                Batch: {{ $program->batch }} | 
                                {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }} - 
                                {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Semua program sudah di-assign</p>
            @endif
        </div>

        <div class="flex space-x-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan Assignment
            </button>
            <a href="{{ route('instructors.show', $instructor) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
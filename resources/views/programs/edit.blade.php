{{-- resources/views/programs/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Program')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.programs.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Program Pelatihan</h2>

        <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="master_program_id" class="block text-sm font-medium text-gray-700 mb-1">Program <span class="text-red-500">*</span></label>
                <select name="master_program_id" id="master_program_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('master_program_id') border-red-500 @enderror">
                    <option value="">Pilih Program</option>
                    @foreach($masterPrograms as $mp)
                    <option value="{{ $mp->id }}" {{ old('master_program_id', $program->master_program_id) == $mp->id ? 'selected' : '' }}>
                        {{ $mp->code }} - {{ $mp->name }}
                    </option>
                    @endforeach
                </select>
                @error('master_program_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="batch" class="block text-sm font-medium text-gray-700 mb-1">Batch <span class="text-red-500">*</span></label>
                <input type="text" name="batch" id="batch" value="{{ old('batch', $program->batch) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('batch') border-red-500 @enderror">
                @error('batch')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $program->start_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $program->end_date->format('Y-m-d')) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="planned" {{ old('status', $program->status) == 'planned' ? 'selected' : '' }}>Rencana</option>
                        <option value="ongoing" {{ old('status', $program->status) == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                        <option value="completed" {{ old('status', $program->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-1">Maks. Peserta</label>
                    <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants', $program->max_participants) }}" min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.programs.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
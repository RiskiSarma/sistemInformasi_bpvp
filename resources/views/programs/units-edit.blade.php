{{-- resources/views/programs/units-edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Unit Kompetensi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.programs.units') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Unit Kompetensi</h2>

        <form method="POST" action="{{ route('admin.programs.units.update', $unit) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="master_program_id" class="block text-sm font-medium text-gray-700 mb-1">Program <span class="text-red-500">*</span></label>
                <select name="master_program_id" id="master_program_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('master_program_id') border-red-500 @enderror">
                    <option value="">Pilih Program</option>
                    @foreach($masterPrograms as $mp)
                    <option value="{{ $mp->id }}" {{ old('master_program_id', $unit->master_program_id) == $mp->id ? 'selected' : '' }}>
                        {{ $mp->code }} - {{ $mp->name }}
                    </option>
                    @endforeach
                </select>
                @error('master_program_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Unit <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $unit->code) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Unit <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $unit->description) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Jelaskan kompetensi yang akan dicapai dalam unit ini</p>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.programs.units') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
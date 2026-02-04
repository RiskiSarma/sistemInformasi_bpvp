@extends('layouts.app')

@section('title', 'Edit Unit Kompetensi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.independent-units.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Unit Kompetensi</h2>

        <form method="POST" action="{{ route('admin.independent-units.update', $unit) }}" class="space-y-6">
            @csrf
            @method('PUT')

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
            </div>

            <!-- Pilih SKKNI yang sudah ada -->
            <div class="mt-6">
                <label for="skkni_id" class="block text-sm font-medium text-gray-700">Pilih SKKNI (Parent)</label>
                <select name="skkni_id" id="skkni_id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">-- Pilih SKKNI yang sudah ada --</option>
                    @foreach($skknis as $skkni)
                        <option value="{{ $skkni->id }}" {{ old('skkni_id', $unit->skkni_id) == $skkni->id ? 'selected' : '' }}>
                            {{ $skkni->nomor }} - {{ $skkni->skkni }} ({{ $skkni->tanggal }} - Berlaku: {{ $skkni->berlaku }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tambah SKKNI baru -->
            <div class="mt-6 border-t pt-4">
                <h3 class="text-lg font-medium text-gray-700">Atau Tambah SKKNI Baru</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div>
                        <label for="nomor" class="block text-sm font-medium text-gray-700">Nomor SKKNI</label>
                        <input type="text" name="nomor" id="nomor" value="{{ old('nomor') }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label for="berlaku" class="block text-sm font-medium text-gray-700">Berlaku</label>
                        <select name="berlaku" id="berlaku" class="w-full px-3 py-2 border rounded-lg">
                            <option value="Y" {{ old('berlaku') == 'Y' ? 'selected' : '' }}>Ya</option>
                            <option value="N" {{ old('berlaku') == 'N' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sync ke pivot -->
            <div class="mt-6">
                <label for="program_pelatihan_id" class="block text-sm font-medium text-gray-700">Program Master (untuk pivot)</label>
                <select name="program_pelatihan_id" id="program_pelatihan_id" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">-- Opsional --</option>
                    @foreach($masterPrograms as $mp)
                        <option value="{{ $mp->id }}" {{ old('program_pelatihan_id', $unit->program_pelatihan_id ?? '') == $mp->id ? 'selected' : '' }}>
                            {{ $mp->code }} - {{ $mp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <label for="jp" class="block text-sm font-medium text-gray-700">JP (untuk pivot)</label>
                <input type="number" name="jp" id="jp" value="{{ old('jp') }}" min="0" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.independent-units.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
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
@extends('layouts.app')

@section('title', 'Edit Master Program')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.programs.master') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Master Program</h2>

        <form method="POST" action="{{ route('admin.programs.master.update', $masterProgram) }}" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $masterProgram->code) }}" required 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Program <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $masterProgram->name) }}" required 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kejuruan_id" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
                    <select name="kejuruan_id" id="kejuruan_id" required 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Kejuruan --</option>
                        @foreach(App\Models\Kejuruan::all() as $kejuruan)
                            <option value="{{ $kejuruan->id }}" {{ old('kejuruan_id', $masterProgram->kejuruan_id) == $kejuruan->id ? 'selected' : '' }}>
                                {{ $kejuruan->kejuruan }}
                            </option>
                        @endforeach
                    </select>
                    @error('kejuruan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bidang_pelatihan_id" class="block text-sm font-medium text-gray-700 mb-1">Bidang Pelatihan <span class="text-red-500">*</span></label>
                    <select name="bidang_pelatihan_id" id="bidang_pelatihan_id" required 
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach(App\Models\BidangPelatihan::all() as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_pelatihan_id', $masterProgram->bidang_pelatihan_id) == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->bidang_pelatihan }}
                            </option>
                        @endforeach
                    </select>
                    @error('bidang_pelatihan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', $masterProgram->duration_hours) }}" required min="1" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('duration_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="versi" class="block text-sm font-medium text-gray-700 mb-1">Versi</label>
                    <input type="number" name="versi" id="versi" value="{{ old('versi', $masterProgram->versi ?? 1) }}" min="1" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('versi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Efektif</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $masterProgram->tanggal ? $masterProgram->tanggal->format('Y-m-d') : '') }}" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('tanggal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_program" class="block text-sm font-medium text-gray-700 mb-1">File Program (PDF/Doc)</label>
                    <input type="file" name="file_program" id="file_program" accept=".pdf,.doc,.docx" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @if($masterProgram->file_program)
                        <p class="mt-2 text-sm text-gray-600">
                            File saat ini: <a href="{{ Storage::url($masterProgram->file_program) }}" target="_blank" class="text-blue-600 hover:underline">Lihat File</a>
                        </p>
                    @endif
                    @error('file_program')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3" 
                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $masterProgram->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $masterProgram->is_active) ? 'checked' : '' }} 
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Program Aktif</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.programs.master') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Master Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
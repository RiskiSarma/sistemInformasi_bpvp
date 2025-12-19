@extends('layouts.app')

@section('title', 'Tambah Instruktur')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.instructors.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Instruktur dari User Terdaftar</h2>

        <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih User (Instruktur yang sudah dibuat di Manajemen User) <span class="text-red-500">*</span></label>
                <select name="user_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data tambahan instruktur -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="expertise" class="block text-sm font-medium text-gray-700 mb-1">Keahlian <span class="text-red-500">*</span></label>
                    <input type="text" name="expertise" value="{{ old('expertise') }}" required class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>

            <div>
                <label for="education" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                <textarea name="education" rows="2" class="w-full px-3 py-2 border rounded-lg">{{ old('education') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Pengalaman (Tahun)</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years') }}" min="0" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.instructors.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Instruktur</button>
            </div>
        </form>
    </div>
</div>
@endsection
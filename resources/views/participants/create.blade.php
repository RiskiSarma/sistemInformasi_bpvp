@extends('layouts.app')

@section('title', 'Tambah Peserta')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.participants.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Peserta dari User Terdaftar</h2>
            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-red-800">Ada kesalahan input:</span>
                </div>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        <form method="POST" action="{{ route('admin.participants.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih User (Peserta yang sudah registrasi) <span class="text-red-500">*</span></label>
                <select name="user_id" id="user_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
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

            <div>
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">Program Pelatihan <span class="text-red-500">*</span></label>
                <select name="program_id" id="program_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-500 @enderror">
                    <option value="">Pilih Program</option>
                    @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->masterProgram->name ?? 'N/A' }} - {{ $program->batch }}
                    </option>
                    @endforeach
                </select>
                @error('program_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Data tambahan -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Kota / Kabupaten">
                    @error('birth_place') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="w-full px-3 py-2 border rounded-lg">
                    @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div>
                <label for="education" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <select name="education" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Pilih</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="SMA">SMA/SMK</option>
                    <option value="D3">D3</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                </select>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="active">Aktif</option>
                    <option value="graduated">Lulus</option>
                    <option value="dropout">Dropout</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.participants.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Peserta</button>
            </div>
        </form>
    </div>
</div>
@endsection
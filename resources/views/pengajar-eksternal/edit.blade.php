@extends('layouts.app')

@section('title', 'Edit Pengajar Eksternal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.pengajar-eksternal.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Pengajar Eksternal</h2>

        <form method="POST" action="{{ route('admin.pengajar-eksternal.update', $pengajarEksternal) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Data Pribadi -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Pribadi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                                name="nama"
                                value="{{ old('nama', $pengajarEksternal->nama) }}"
                                required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                        @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                                name="nik"
                                value="{{ old('nik', $pengajarEksternal->nik) }}"
                                required
                                maxlength="16"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nik') border-red-500 @enderror">
                        @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            NIP<span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                                name="nip"
                                value="{{ old('nip', $pengajarEksternal->nip) }}"
                                required
                                maxlength="16"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nip') border-red-500 @enderror">
                        @error('nip')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Data Institusi -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Institusi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Instansi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="instansi" 
                               value="{{ old('instansi', $pengajarEksternal->instansi) }}"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('instansi') border-red-500 @enderror">
                        @error('instansi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jabatan
                        </label>
                        <input type="text" 
                               name="jabatan" 
                               value="{{ old('jabatan', $pengajarEksternal->jabatan) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('jabatan') border-red-500 @enderror">
                        @error('jabatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Alamat
                        </label>
                        <textarea name="alamat" 
                                  rows="3"
                                  class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $pengajarEksternal->alamat) }}</textarea>
                        @error('alamat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="border-b pb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kontak</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="telepon" 
                               value="{{ old('telepon', $pengajarEksternal->telepon) }}"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('telepon') border-red-500 @enderror">
                        @error('telepon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $pengajarEksternal->email) }}"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="pendidikan_id" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                        <select name="pendidikan_id" id="pendidikan_id" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Pendidikan --</option>
                            @foreach($pendidikans as $pend)
                                <option value="{{ $pend->id }}" {{ old('pendidikan_id', $pengajarEksternal->pendidikan_id) == $pend->id ? 'selected' : '' }}>
                                    {{ $pend->pendidikan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pendidikan -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pendidikan</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Jenjang Pendidikan
                        </label>
                        <select name="pendidikan_id"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('pendidikan_id') border-red-500 @enderror">
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($pendidikans as $pendidikan)
                                <option value="{{ $pendidikan->id }}" 
                                        {{ old('pendidikan_id', $pengajarEksternal->pendidikan_id) == $pendidikan->id ? 'selected' : '' }}>
                                    {{ $pendidikan->jenjang_pendidikan }}
                                </option>
                            @endforeach
                        </select>
                        @error('pendidikan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Kejuruan/Bidang Studi
                        </label>
                        <input type="text" 
                               name="kejuruan_pendidikan" 
                               value="{{ old('kejuruan_pendidikan', $pengajarEksternal->kejuruan_pendidikan) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('kejuruan_pendidikan') border-red-500 @enderror">
                        @error('kejuruan_pendidikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.pengajar-eksternal.index') }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Pengajar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
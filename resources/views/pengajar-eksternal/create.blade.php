@extends('layouts.app')

@section('title', 'Tambah Pengajar Eksternal')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Tambah Pengajar Eksternal</h2>
        <p class="text-gray-600 mt-1">Tambahkan pengajar dari luar instansi</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-red-800 font-semibold mb-2">Terjadi kesalahan:</h3>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengajar-eksternal.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Data Pribadi -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Data Pribadi</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        NIK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nik') border-red-500 @enderror">
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        NIP <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nip" value="{{ old('nip') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nip') border-red-500 @enderror">
                    @error('nip')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Telepon <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="telepon" value="{{ old('telepon') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('telepon') border-red-500 @enderror">
                    @error('telepon')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Instansi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="instansi" value="{{ old('instansi') }}" required
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('instansi') border-red-500 @enderror">
                    @error('instansi')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                    <select name="pendidikan_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach($pendidikans as $pendidikan)
                            <option value="{{ $pendidikan->id }}" {{ old('pendidikan_id') == $pendidikan->id ? 'selected' : '' }}>
                                {{ $pendidikan->pendidikan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kejuruan Pendidikan</label>
                    <input type="text" name="kejuruan_pendidikan" value="{{ old('kejuruan_pendidikan') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Akun Login (Optional) -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Akun Login (Opsional)</h3>
                    <p class="text-sm text-gray-600 mt-1">Buat akun agar pengajar eksternal bisa login ke sistem</p>
                </div>
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="create_user_account" id="create_user_account" value="1" 
                           {{ old('create_user_account') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Buat Akun</span>
                </label>
            </div>

            <div id="user_account_fields" class="{{ old('create_user_account') ? '' : 'hidden' }} space-y-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-sm text-blue-800">
                        <strong>Info:</strong> Email yang sama akan digunakan untuk login. 
                        Akun akan dibuat dengan role <strong>Instructor</strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.pengajar-eksternal.index') }}" 
               class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Simpan Pengajar Eksternal
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('create_user_account');
    const fields = document.getElementById('user_account_fields');
    const passwordInput = document.getElementById('password');
    const passwordConfInput = document.getElementById('password_confirmation');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            fields.classList.remove('hidden');
            passwordInput.required = true;
            passwordConfInput.required = true;
        } else {
            fields.classList.add('hidden');
            passwordInput.required = false;
            passwordConfInput.required = false;
            passwordInput.value = '';
            passwordConfInput.value = '';
        }
    });
});
</script>
@endsection
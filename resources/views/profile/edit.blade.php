@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Information -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Profil</h3>
        
        <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Update Password -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
        
        <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror">
                @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="password" name="password" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-red-600 mb-2">Hapus Akun</h3>
        <p class="text-sm text-gray-600 mb-4">
            Setelah akun Anda dihapus, semua data dan informasi akan dihapus secara permanen. Pastikan Anda sudah mengunduh data yang diperlukan sebelum menghapus akun.
        </p>
        
        <form method="POST" action="{{ route('admin.profile.destroy') }}" x-data="{ showModal: false }">
            @csrf
            @method('DELETE')

            <button type="button" @click="showModal = true" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Hapus Akun
            </button>

            <!-- Confirmation Modal -->
            <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>
                    
                    <div class="relative bg-white rounded-lg max-w-md w-full p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Penghapusan Akun</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Apakah Anda yakin ingin menghapus akun? Masukkan password untuk konfirmasi.
                        </p>
                        
                        <div class="mb-4">
                            <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="delete_password" name="password" 
                                   class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-red-500">
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Hapus Akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
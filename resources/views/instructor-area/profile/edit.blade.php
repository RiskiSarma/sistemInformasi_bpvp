@extends('layouts.instructor')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-6 max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Profil -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pribadi</h3>
                <form action="{{ route('instructor.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border rounded-lg @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ganti Password -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h3>
                <form action="{{ route('instructor.profile.password') }}" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-2 border rounded-lg @error('current_password') border-red-500 @enderror">
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password" required class="w-full px-4 py-2 border rounded-lg @error('password') border-red-500 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Profil Instruktur -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h4 class="mt-4 text-lg font-semibold text-gray-800">{{ $user->name }}</h4>
                <p class="text-gray-600">Instruktur</p>
                <p class="text-sm text-gray-500 mt-2">{{ $user->email }}</p>
            </div>

            @if($instructor)
            <div class="mt-6 pt-6 border-t space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Keahlian</span>
                    <span class="font-medium">{{ $instructor->expertise ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Telepon</span>
                    <span class="font-medium">{{ $instructor->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $instructor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($instructor->status ?? 'inactive') }}
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.participant')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-6 max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Edit Profil -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pribadi</h3>
                <form action="{{ route('participant.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2 border rounded-lg @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" value="{{ old('nik', $participant->nik ?? '') }}" required maxlength="16" class="w-full px-4 py-2 border rounded-lg @error('nik') border-red-500 @enderror" placeholder="16 digit NIK">
                            @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $participant->phone ?? '') }}" class="w-full px-4 py-2 border rounded-lg">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tambahan: Tempat & Tanggal Lahir -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input 
                                type="text" 
                                name="birth_place" 
                                value="{{ old('birth_place', $participant->birth_place ?? '') }}" 
                                class="w-full px-4 py-2 border rounded-lg @error('birth_place') border-red-500 @enderror"
                                placeholder="Kota / Kabupaten">
                            @error('birth_place') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input 
                                type="date" 
                                name="birth_date" 
                                value="{{ old('birth_date', $participant->birth_date ? $participant->birth_date->format('Y-m-d') : '') }}" 
                                class="w-full px-4 py-2 border rounded-lg @error('birth_date') border-red-500 @enderror">
                            @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                <form action="{{ route('participant.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

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

        <!-- Card Profil – tambahkan tampilan tempat & tgl lahir di sini juga -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h4 class="mt-4 text-lg font-semibold text-gray-800">{{ $user->name }}</h4>
                <p class="text-gray-600">Peserta Pelatihan</p>
                <p class="text-sm text-gray-500 mt-2">{{ $user->email }}</p>

                @if($participant ?? false)
                <div class="mt-6 pt-6 border-t space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">NIK</span>
                        <span class="font-medium">{{ $participant->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tempat Lahir</span>
                        <span class="font-medium">{{ $participant->birth_place ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tanggal Lahir</span>
                        <span class="font-medium">
                            {{ $participant->birth_date ? $participant->birth_date->format('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Program</span>
                        <span class="font-medium">{{ $participant->program?->masterProgram?->name ?? '-' }} {{ $participant->program?->batch ? '(Batch '.$participant->program->batch.')' : '' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : ($participant->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($participant->status ?? '-') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
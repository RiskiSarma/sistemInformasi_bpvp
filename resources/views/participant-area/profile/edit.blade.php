@extends('layouts.participant')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-6 max-w-4xl">
    <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>

    {{-- @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif --}}

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

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
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2 border rounded-lg @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $participant->nik ?? '') }}"
                                maxlength="16"
                                class="w-full px-4 py-2 border rounded-lg @error('nik') border-red-500 @enderror"
                                placeholder="16 digit NIK">
                            @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2 border rounded-lg @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $participant->phone ?? '') }}"
                                class="w-full px-4 py-2 border rounded-lg">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="birth_place"
                                value="{{ old('birth_place', $participant->birth_place ?? '') }}"
                                class="w-full px-4 py-2 border rounded-lg @error('birth_place') border-red-500 @enderror"
                                placeholder="Kota / Kabupaten">
                            @error('birth_place') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="birth_date"
                                value="{{ old('birth_date', $participant->birth_date ? $participant->birth_date->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-2 border rounded-lg @error('birth_date') border-red-500 @enderror">
                            @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pendidikan Terakhir --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                            <select name="pendidikan_id"
                                class="w-full px-4 py-2 border rounded-lg @error('pendidikan_id') border-red-500 @enderror">
                                <option value="">-- Pilih Pendidikan --</option>
                                @foreach(\App\Models\Pendidikan::orderBy('pendidikan')->get() as $pend)
                                    <option value="{{ $pend->id }}"
                                        {{ old('pendidikan_id', $participant->pendidikan_id ?? '') == $pend->id ? 'selected' : '' }}>
                                        {{ $pend->pendidikan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pendidikan_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                            <select name="gender"
                                class="w-full px-4 py-2 border rounded-lg @error('gender') border-red-500 @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="LAKI-LAKI" {{ old('gender', $participant->gender ?? '') == 'LAKI-LAKI' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="PEREMPUAN" {{ old('gender', $participant->gender ?? '') == 'PEREMPUAN' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Alamat (full width) --}}
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-2 border rounded-lg @error('address') border-red-500 @enderror"
                            placeholder="Alamat lengkap sesuai KTP">{{ old('address', $participant->address ?? '') }}</textarea>
                        @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-2 border rounded-lg @error('current_password') border-red-500 @enderror">
                            @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-2 border rounded-lg @error('password') border-red-500 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-2 border rounded-lg">
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

        <!-- Card Info Profil -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="text-center">
                <div class="w-24 h-24 bg-blue-600 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h4 class="mt-4 text-lg font-semibold text-gray-800">{{ $user->name }}</h4>
                <p class="text-gray-600">Peserta Pelatihan</p>
                <p class="text-sm text-gray-500 mt-2">{{ $user->email }}</p>

                @if($participant ?? false)
                <div class="mt-6 pt-6 border-t space-y-3 text-sm text-left">
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">NIK</span>
                        <span class="font-medium text-right">{{ $participant->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Tempat Lahir</span>
                        <span class="font-medium text-right">{{ $participant->birth_place ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Tanggal Lahir</span>
                        <span class="font-medium text-right">
                            {{ $participant->birth_date ? $participant->birth_date->format('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Pendidikan</span>
                        <span class="font-medium text-right">
                            {{ $participant->pendidikan->pendidikan ?? '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Alamat</span>
                        <span class="font-medium text-right">{{ $participant->address ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Program</span>
                        {{-- Tampilkan nama program + angkatan --}}
                        <span class="font-medium text-right">
                            {{ $participant->program?->masterProgram?->name ?? '-' }}
                            @if($participant->program?->angkatan)
                                {{ $participant->program->angkatan }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-gray-500 shrink-0">Status</span>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' :
                            ($participant->status === 'graduated' ? 'bg-blue-100 text-blue-800' :
                            'bg-red-100 text-red-800') }}">
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
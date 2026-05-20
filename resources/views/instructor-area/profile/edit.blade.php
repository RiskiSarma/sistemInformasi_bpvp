@extends('layouts.instructor')

@section('title', 'Profil Saya')

@section('content')
<div class="space-y-6 max-w-6xl">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Profil Saya</h2>
            <p class="text-gray-600 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
        </div>
        @if($instructor)
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                Instruktur Internal
            </span>
        @elseif($pengajarEksternal)
            <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-full text-sm font-medium">
                Pengajar Eksternal
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Profil -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Pribadi -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Pribadi
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('instructor.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Telepon
                                </label>
                                <input type="text" name="phone" 
                                    value="{{ old('phone', $instructor->phone ?? $pengajarEksternal->telepon ?? '') }}" 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="08xxxxxxxxxx">
                            </div>

                            <!-- Pendidikan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Pendidikan Terakhir
                                </label>
                                <select name="pendidikan_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikans as $pendidikan)
                                        <option value="{{ $pendidikan->id }}" 
                                            {{ old('pendidikan_id', $instructor->pendidikan_id ?? $pengajarEksternal->pendidikan_id ?? '') == $pendidikan->id ? 'selected' : '' }}>
                                            {{-- ✅ Sesuaikan dengan nama kolom yang ada --}}
                                            {{ $pendidikan->nama ?? $pendidikan->pendidikan ?? $pendidikan->name ?? 'Pendidikan #'.$pendidikan->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if($instructor)
                                <!-- Keahlian (Instruktur Internal) -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Bidang Keahlian
                                    </label>
                                    <input type="text" name="expertise" 
                                        value="{{ old('expertise', $instructor->expertise) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Contoh: Teknik Otomotif, Elektronika, dll">
                                </div>

                                <!-- Pengalaman Mengajar -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Pengalaman Mengajar (Tahun)
                                    </label>
                                    <input type="number" name="experience_years" min="0"
                                        value="{{ old('experience_years', $instructor->experience_years) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="0">
                                </div>
                            @endif

                            @if($pengajarEksternal)
                                <!-- NIK (Pengajar Eksternal) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        NIK
                                    </label>
                                    <input type="text" name="nik" 
                                        value="{{ old('nik', $pengajarEksternal->nik) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="16 digit NIK">
                                </div>

                                <!-- NIP -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        NIP
                                    </label>
                                    <input type="text" name="nip" 
                                        value="{{ old('nip', $pengajarEksternal->nip) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Nomor Induk Pegawai">
                                </div>

                                <!-- Instansi -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Instansi
                                    </label>
                                    <input type="text" name="instansi" 
                                        value="{{ old('instansi', $pengajarEksternal->instansi) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Nama instansi/perusahaan">
                                </div>

                                <!-- Jabatan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Jabatan
                                    </label>
                                    <input type="text" name="jabatan" 
                                        value="{{ old('jabatan', $pengajarEksternal->jabatan) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Jabatan di instansi">
                                </div>

                                <!-- Kejuruan Pendidikan -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Kejuruan/Jurusan Pendidikan
                                    </label>
                                    <input type="text" name="kejuruan_pendidikan" 
                                        value="{{ old('kejuruan_pendidikan', $pengajarEksternal->kejuruan_pendidikan) }}" 
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Contoh: Teknik Mesin, Manajemen, dll">
                                </div>

                                <!-- Alamat -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Alamat Lengkap
                                    </label>
                                    <textarea name="alamat" rows="3"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                        placeholder="Alamat lengkap">{{ old('alamat', $pengajarEksternal->alamat) }}</textarea>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b bg-gradient-to-r from-green-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Keamanan Akun
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Ubah password untuk menjaga keamanan akun Anda</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('instructor.profile.password') }}" method="POST">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password Saat Ini <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="current_password" required 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 @error('current_password') border-red-500 @enderror"
                                    placeholder="Masukkan password lama">
                                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Password Baru <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" required 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 @error('password') border-red-500 @enderror"
                                    placeholder="Minimal 8 karakter">
                                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password_confirmation" required 
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500"
                                    placeholder="Ketik ulang password baru">
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Profil Sidebar -->
        <div class="space-y-6">
            <!-- Card Profil -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-teal-600 to-blue-800 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h4 class="mt-4 text-lg font-semibold text-gray-800">{{ $user->name }}</h4>
                    <p class="text-gray-600 text-sm mt-1">
                        @if($instructor)
                            Instruktur Internal
                        @elseif($pengajarEksternal)
                            Pengajar Eksternal
                        @else
                            Instruktur
                        @endif
                    </p>
                    <p class="text-sm text-gray-500 mt-2 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $user->email }}
                    </p>
                </div>

                @if($instructor)
                <div class="mt-6 pt-6 border-t space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Status
                        </span>
                        <span class="px-2 py-1 text-xs rounded-full font-medium {{ $instructor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $instructor->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    @if($instructor->expertise)
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Keahlian
                        </span>
                        <span class="font-medium text-right">{{ $instructor->expertise }}</span>
                    </div>
                    @endif
                    @if($instructor->phone)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Telepon
                        </span>
                        <span class="font-medium">{{ $instructor->phone }}</span>
                    </div>
                    @endif
                    @if($instructor->experience_years)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pengalaman
                        </span>
                        <span class="font-medium">{{ $instructor->experience_years }} tahun</span>
                    </div>
                    @endif
                    @if($instructor->pendidikan)
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            Pendidikan
                        </span>
                        <span class="font-medium text-right">{{ $instructor->pendidikan->nama }}</span>
                    </div>
                    @endif
                </div>
                @endif

                @if($pengajarEksternal)
                <div class="mt-6 pt-6 border-t space-y-3">

                    <!-- Instansi -->
                    @if($pengajarEksternal->instansi)
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Instansi
                        </span>
                        <span class="font-medium text-right">{{ $pengajarEksternal->instansi }}</span>
                    </div>
                    @endif

                    <!-- Jabatan -->
                    @if($pengajarEksternal->jabatan)
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Jabatan
                        </span>
                        <span class="font-medium text-right">{{ $pengajarEksternal->jabatan }}</span>
                    </div>
                    @endif

                    <!-- Telepon -->
                    @if($pengajarEksternal->telepon)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Telepon
                        </span>
                        <span class="font-medium">{{ $pengajarEksternal->telepon }}</span>
                    </div>
                    @endif

                    <!-- Pendidikan -->
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            Pendidikan
                        </span>
                        <span class="font-medium text-right">
                            @if($pengajarEksternal->pendidikan)
                                {{ $pengajarEksternal->pendidikan->nama ?? $pengajarEksternal->pendidikan->pendidikan ?? '-' }}
                            @elseif($pengajarEksternal->pendidikan_id)
                                @php
                                    $pend = \App\Models\Pendidikan::find($pengajarEksternal->pendidikan_id);
                                @endphp
                                {{ $pend ? ($pend->nama ?? $pend->pendidikan ?? 'S1/D4') : 'S1/D4' }}
                            @else
                                <span class="text-gray-400">Belum diisi</span>
                            @endif
                        </span>
                    </div>

                    <!-- Kejuruan Pendidikan -->
                    @if($pengajarEksternal->kejuruan_pendidikan)
                    <div class="flex items-start justify-between text-sm">
                        <span class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Jurusan
                        </span>
                        <span class="font-medium text-right">{{ $pengajarEksternal->kejuruan_pendidikan }}</span>
                    </div>
                    @endif

                </div>
                @endif
            </div>

            <!-- Quick Stats (Opsional) -->
            <div class="bg-gradient-to-br from-teal-600 to-blue-800 rounded-lg shadow-sm p-6 text-white">
                <h4 class="font-semibold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Ringkasan Aktivitas
                </h4>
                <div class="space-y-3">
                    @if($instructor)
                        @php
                            $totalPrograms = $instructor->programInstructors()->count();
                            $activePrograms = $instructor->programInstructors()->whereHas('program', function($q) {
                                $q->where('status', 'ongoing');
                            })->count();
                        @endphp
                    @elseif($pengajarEksternal)
                        @php
                            $totalPrograms = \App\Models\ProgramInstructor::where('pengajar_eksternal_id', $pengajarEksternal->id)->count();
                            $activePrograms = \App\Models\ProgramInstructor::where('pengajar_eksternal_id', $pengajarEksternal->id)
                                ->whereHas('program', function($q) {
                                    $q->where('status', 'ongoing');
                                })->count();
                        @endphp
                    @endif
                    
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Total Program</span>
                        <span class="text-2xl font-bold">{{ $totalPrograms ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-blue-100">Program Aktif</span>
                        <span class="text-2xl font-bold">{{ $activePrograms ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
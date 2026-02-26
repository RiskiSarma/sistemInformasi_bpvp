@extends('layouts.app')

@section('title', 'Detail Pengajar Eksternal')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.pengajar-eksternal.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-6">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-3xl font-bold text-orange-600">{{ substr($pengajarEksternal->nama, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $pengajarEksternal->nama }}</h2>
                    <p class="text-orange-600 mt-1 font-medium">{{ $pengajarEksternal->jabatan ?? 'Pengajar Eksternal' }}</p>
                    <p class="text-gray-600 mt-1">{{ $pengajarEksternal->instansi }}</p>
                    
                    <div class="flex items-center space-x-4 mt-3 text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $pengajarEksternal->telepon }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $pengajarEksternal->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.pengajar-eksternal.edit', $pengajarEksternal) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    Edit Data
                </a>
                <form action="{{ route('admin.pengajar-eksternal.destroy', $pengajarEksternal) }}" 
                      method="POST" 
                      onsubmit="return confirm('Yakin ingin menghapus pengajar ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detail Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Data Pribadi
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">NIK</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">NIPI</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->nipi ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Institusi -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Data Institusi
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Instansi</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->instansi }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jabatan</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->jabatan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Alamat</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Pendidikan -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                    Pendidikan
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Jenjang</p>
                        <p class="font-medium text-gray-900">
                            {{ $pengajarEksternal->pendidikan->jenjang_pendidikan ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kejuruan/Bidang Studi</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->kejuruan_pendidikan ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Program yang Diampu -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Program yang Diampu</h3>
                @if($pengajarEksternal->programs->count() > 0)
                    <div class="space-y-3">
                        @foreach($pengajarEksternal->programs as $program)
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="font-medium text-sm text-gray-900">
                                {{ $program->masterProgram->name ?? 'Program' }}
                            </p>
                            <p class="text-xs text-gray-600 mt-1">{{ $program->batch }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Belum mengampu program</p>
                @endif
            </div>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Sistem</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-600">Ditambahkan oleh</p>
                        <p class="font-medium text-gray-900">{{ $pengajarEksternal->user->name ?? 'Sistem' }}</p>
                        <p class="text-xs text-gray-500">{{ $pengajarEksternal->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Terakhir diupdate</p>
                        <p class="text-xs text-gray-500">{{ $pengajarEksternal->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Card -->
<div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="text-gray-600 mt-2">Semoga harimu menyenangkan di Balai Latihan Kerja</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-graduate text-blue-600 text-3xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
    <!-- Card 1 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Siswa</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalSiswa }}</h3>
                <p class="text-green-600 text-xs mt-1">
                    <i class="fas fa-arrow-up"></i> +{{ $siswaGrowth }}% dari bulan lalu
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Program</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalProgram }}</h3>
                <p class="text-blue-600 text-xs mt-1">
                    <i class="fas fa-minus"></i> Tidak ada perubahan
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-book text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Instruktur</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalInstruktur }}</h3>
                <p class="text-green-600 text-xs mt-1">
                    <i class="fas fa-arrow-up"></i> +{{ $instrukturBaru }} instruktur baru
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Sertifikat</p>
                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalSertifikat }}</h3>
                <p class="text-green-600 text-xs mt-1">
                    <i class="fas fa-arrow-up"></i> +{{ $sertifikatGrowth }}% dari bulan lalu
                </p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-certificate text-yellow-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><strong>Siswa baru</strong> terdaftar</p>
                    <p class="text-xs text-gray-500">5 menit yang lalu</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-green-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><strong>Program</strong> Desain Grafis selesai</p>
                    <p class="text-xs text-gray-500">2 jam yang lalu</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-certificate text-purple-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800"><strong>15 Sertifikat</strong> diterbitkan</p>
                    <p class="text-xs text-gray-500">3 jam yang lalu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Aksi Cepat</h3>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="{{ route('siswa.create') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Tambah Siswa</p>
            </a>
            
            <a href="{{ route('program.create') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-plus-circle text-green-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Buat Program</p>
            </a>
            
            <a href="{{ route('laporan.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-file-export text-purple-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Export Data</p>
            </a>
            
            <a href="{{ route('laporan.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-print text-yellow-600 text-2xl mb-2"></i>
                <p class="text-sm font-medium text-gray-800">Cetak Laporan</p>
            </a>
        </div>
    </div>
</div>
@endsection
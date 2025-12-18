@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
        <p class="text-gray-600 mt-1">Generate dan export laporan data</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Generate Laporan</h3>
        <p class="text-sm text-gray-500 mt-2">
            Laporan Program Pelatihan akan menampilkan semua program (planned, ongoing, completed) sesuai rentang tanggal.
        </p>
        <form method="POST" action="{{ route('admin.reports.generate') }}" class="space-y-6">
            @csrf
            
            <div>
                <label for="report_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                <select name="report_type" id="report_type" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Pilih Jenis Laporan</option>
                    <option value="program">Laporan Program Pelatihan</option>
                    <option value="participant">Laporan Peserta</option>
                    <option value="attendance">Laporan Kehadiran</option>
                    <option value="certificate">Laporan Sertifikat</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center space-x-3 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Generate Preview
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Reports -->
    <div>
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Laporan Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('admin.reports.active-programs') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition cursor-pointer block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">{{ $stats['active_programs'] ?? 0 }}</span>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1">Program Aktif (Ongoing)</h4>
                <p class="text-sm text-gray-600">Jumlah program dengan status ongoing</p>
            </a>

            <a href="{{ route('admin.reports.active-participants') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition cursor-pointer block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-green-600">{{ $stats['active_participants'] ?? 0 }}</span>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1">Peserta Aktif</h4>
                <p class="text-sm text-gray-600">Data peserta yang masih aktif</p>
            </a>

            <a href="{{ route('admin.reports.attendance-month') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition cursor-pointer block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-purple-600">{{ $stats['attendance_this_month'] ?? 0 }}</span>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1">Kehadiran Bulan Ini</h4>
                <p class="text-sm text-gray-600">Rekap kehadiran periode berjalan</p>
            </a>

            <a href="{{ route('admin.reports.certificates') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition cursor-pointer block">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-yellow-600">{{ $stats['certificates_issued'] ?? 0 }}</span>
                </div>
                <h4 class="font-semibold text-gray-800 mb-1">Sertifikat Terbit</h4>
                <p class="text-sm text-gray-600">Total sertifikat yang diterbitkan</p>
            </a>
        </div>
    </div>
</div>
@endsection
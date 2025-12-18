@extends('layouts.app')

@section('title', 'Rekap Kehadiran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Rekap Kehadiran</h2>
            <p class="text-gray-600 mt-1">Statistik kehadiran per program</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Recap Cards -->
    @forelse($programs as $program)
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-6 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-gray-800">{{ $program->masterProgram->name ?? 'N/A' }} - {{ $program->batch }}</h3>
            <p class="text-sm text-gray-600">{{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hadir</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tidak Hadir</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Terlambat</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Izin</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($program->attendance_stats as $stat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $stat['participant']->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">{{ $stat['total'] }}</td>
                        <td class="px-6 py-4 text-center text-green-600 font-medium">{{ $stat['present'] }}</td>
                        <td class="px-6 py-4 text-center text-red-600 font-medium">{{ $stat['absent'] }}</td>
                        <td class="px-6 py-4 text-center text-orange-600 font-medium">{{ $stat['late'] }}</td>
                        <td class="px-6 py-4 text-center text-yellow-600 font-medium">{{ $stat['excused'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $stat['percentage'] >= 80 ? 'bg-green-100 text-green-800' : ($stat['percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $stat['percentage'] }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
        <p class="text-gray-500">Tidak ada data kehadiran</p>
    </div>
    @endforelse
</div>
@endsection
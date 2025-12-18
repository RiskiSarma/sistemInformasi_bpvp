@extends('layouts.participant')

@section('title', 'Kehadiran Saya')

@section('content')
@if($stats)
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
        <p class="text-gray-500 text-sm">Total</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-green-50 rounded-lg shadow-sm border border-green-200 p-4 text-center">
        <p class="text-green-600 text-sm">Hadir</p>
        <p class="text-2xl font-bold text-green-800">{{ $stats['present'] }}</p>
    </div>
    <div class="bg-red-50 rounded-lg shadow-sm border border-red-200 p-4 text-center">
        <p class="text-red-600 text-sm">Tidak Hadir</p>
        <p class="text-2xl font-bold text-red-800">{{ $stats['absent'] }}</p>
    </div>
    <div class="bg-orange-50 rounded-lg shadow-sm border border-orange-200 p-4 text-center">
        <p class="text-orange-600 text-sm">Terlambat</p>
        <p class="text-2xl font-bold text-orange-800">{{ $stats['late'] }}</p>
    </div>
    <div class="bg-blue-50 rounded-lg shadow-sm border border-blue-200 p-4 text-center">
        <p class="text-blue-600 text-sm">Izin</p>
        <p class="text-2xl font-bold text-blue-800">{{ $stats['excused'] }}</p>
    </div>
</div>
@endif

<div class="bg-white rounded-lg shadow-sm border overflow-hidden">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Riwayat Kehadiran</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->date->format('d F Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->program->masterProgram->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : ($attendance->status === 'late' ? 'bg-orange-100 text-orange-800' : ($attendance->status === 'excused' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data kehadiran</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($attendances->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $attendances->links() }}
    </div>
    @endif
</div>
@endsection
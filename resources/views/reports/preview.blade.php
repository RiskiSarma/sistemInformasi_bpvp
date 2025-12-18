@extends('layouts.app')

@section('title', 'Preview Laporan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Preview Laporan</h2>
            <p class="text-gray-600 mt-1">
                Jenis: {{ ucfirst($validated['report_type']) }} 
                @if($validated['date_from'] || $validated['date_to'])
                    | Periode: 
                    {{ $validated['date_from'] ? date('d/m/Y', strtotime($validated['date_from'])) : '-' }}
                    s/d
                    {{ $validated['date_to'] ? date('d/m/Y', strtotime($validated['date_to'])) : '-' }}
                @endif
            </p>
        </div>
        <div class="flex space-x-3">
            <form action="{{ route('admin.reports.export', 'pdf') }}" method="GET">
                <input type="hidden" name="report_type" value="{{ $validated['report_type'] }}">
                <input type="hidden" name="date_from" value="{{ $validated['date_from'] ?? '' }}">
                <input type="hidden" name="date_to" value="{{ $validated['date_to'] ?? '' }}">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Export PDF
                </button>
            </form>
            <form action="{{ route('admin.reports.export', 'excel') }}" method="GET">
                <input type="hidden" name="report_type" value="{{ $validated['report_type'] }}">
                <input type="hidden" name="date_from" value="{{ $validated['date_from'] ?? '' }}">
                <input type="hidden" name="date_to" value="{{ $validated['date_to'] ?? '' }}">
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Export Excel
                </button>
            </form>
            <a href="{{ route('admin.reports.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        @if($data->count() > 0)
            @if($validated['report_type'] === 'program')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Peserta</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data as $index => $program)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $program->title ?? $program->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($program->description ?? '-', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $program->duration ?? '-' }} hari</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $program->participants->count() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($validated['report_type'] === 'participant')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data as $index => $participant)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $participant->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $participant->email ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $participant->program->title ?? $participant->program->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($participant->status === 'active') bg-green-100 text-green-800
                                        @elseif($participant->status === 'graduated') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($participant->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($validated['report_type'] === 'attendance')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data as $index => $attendance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->date ? date('d/m/Y', strtotime($attendance->date)) : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->program->title ?? $attendance->program->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $attendance->participant->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($attendance->status === 'present') bg-green-100 text-green-800
                                        @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($attendance->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($validated['report_type'] === 'certificate')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Lulus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($data as $index => $participant)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $participant->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $participant->program->title ?? $participant->program->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $participant->updated_at ? date('d/m/Y', strtotime($participant->updated_at)) : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Diterbitkan</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-4 text-sm text-gray-600">
                Total data: {{ $data->count() }}
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-gray-500">Tidak ada data untuk ditampilkan</p>
            </div>
        @endif
    </div>
</div>
@endsection
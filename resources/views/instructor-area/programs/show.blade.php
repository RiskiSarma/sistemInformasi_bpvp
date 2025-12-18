@extends('layouts.instructor')

@section('title', $program->name)

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">{{ $program->name }}</h2>
        <p class="text-gray-600 mt-1">Detail program pelatihan</p>
    </div>

    <!-- Info Program -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Program</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Kode</dt>
                    <dd class="font-medium">{{ $program->code ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Batch</dt>
                    <dd class="font-medium">{{ $program->batch ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Periode</dt>
                    <dd class="font-medium">{{ $program->start_date->format('d/m/Y') }} - {{ $program->end_date->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Status</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($program->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Jumlah Peserta</dt>
                    <dd class="font-medium">{{ $program->participants->count() }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Instruktur</h3>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                    {{ substr($program->instructor->name ?? '?', 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $program->instructor->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600">{{ $program->instructor->expertise ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Peserta -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Peserta ({{ $program->participants->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($program->participants as $index => $participant)
                    <tr>
                        <td class="px-6 py-4 text-sm">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $participant->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $participant->email ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($participant->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada peserta</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
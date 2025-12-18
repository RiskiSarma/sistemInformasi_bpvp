@extends('layouts.participant')

@section('title', 'Program Saya')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Program Saya</h2>
        <p class="text-gray-600 mt-1">Detail program pelatihan yang Anda ikuti</p>
    </div>

    @if($program ?? false)
    <!-- Info Program -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Program</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Nama Program</dt>
                    <dd class="font-medium">{{ $program->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Batch</dt>
                    <dd class="font-medium">{{ $program->batch ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Periode</dt>
                    <dd class="font-medium">
                        {{ $program->start_date?->format('d/m/Y') ?? '-' }} - 
                        {{ $program->end_date?->format('d/m/Y') ?? '-' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Status Program</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ ($program->status ?? '') === 'ongoing' ? 'bg-green-100 text-green-800' : 
                               (($program->status ?? '') === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ ucfirst($program->status ?? 'unknown') }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Instruktur</dt>
                    <dd class="font-medium">
                        {{ $program->instructor?->name ?? 'Belum ditentukan' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Anda</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Status Keikutsertaan</dt>
                    <dd>
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ ($participant->status ?? '') === 'active' ? 'bg-green-100 text-green-800' : 
                            (($participant->status ?? '') === 'graduated' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($participant->status ?? 'unknown') }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Tanggal Registrasi</dt>
                    <dd class="font-medium">
                        {{ $participant->enrollment_date?->format('d/m/Y') ?? '-' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
        <p class="text-gray-500">Anda belum terdaftar di program pelatihan apapun.</p>
    </div>
    @endif
</div>
@endsection
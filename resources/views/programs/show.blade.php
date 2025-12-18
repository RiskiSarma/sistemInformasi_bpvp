@extends('layouts.app')

@section('title', 'Detail Pelatihan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.programs.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali</span>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Detail Program Pelatihan</h2>
                <p class="text-gray-600 mt-1">Batch {{ $program->batch }}</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.edit', $program) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Program
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Program</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Program</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $program->masterProgram->name ?? 'N/A' }}
                        </dd>
                        <dd class="text-sm text-gray-500">
                            Kode: {{ $program->masterProgram->code ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Batch</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $program->batch }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Periode Pelatihan</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $program->start_date->format('d F Y') }} s/d {{ $program->end_date->format('d F Y') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-3 py-1 text-sm rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kuota Peserta</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $program->participants->count() }} / {{ $program->max_participants ?? 'Tidak dibatasi' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Audit</h3>
                <dl class="space-y-6">
                    <div class="bg-gray-50 px-5 py-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                        <dd class="mt-2 text-lg font-semibold text-gray-900">
                            {{ $program->creator?->name ?? 'Sistem' }}
                        </dd>
                        <dd class="text-sm text-gray-500">
                            {{ $program->created_at->format('d F Y, H:i') }}
                        </dd>
                    </div>

                    <div class="bg-gray-50 px-5 py-4 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui Oleh</dt>
                        <dd class="mt-2 text-lg font-semibold text-gray-900">
                            {{ $program->updater?->name ?? 'Belum pernah diupdate' }}
                        </dd>
                        <dd class="text-sm text-gray-500">
                            {{ $program->updated_at->format('d F Y, H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Peserta (opsional) -->
    @if($program->participants->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Daftar Peserta ({{ $program->participants->count() }})</h3>
        </div>
        <!-- Tambahkan tabel peserta kalau perlu -->
    </div>
    @endif
</div>
@endsection
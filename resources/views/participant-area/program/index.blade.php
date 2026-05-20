@extends('layouts.participant')

@section('title', 'Program Saya')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Program Saya</h2>
        <p class="text-gray-600 mt-1">
            {{ $participants->count() > 1 ? 'Daftar program pelatihan yang Anda ikuti' : 'Program pelatihan yang Anda ikuti' }}
        </p>
    </div>

    @if($participants->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500">Anda belum terdaftar di program pelatihan apapun.</p>
        <p class="text-sm text-gray-400 mt-1">Silakan hubungi admin untuk mendaftar.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($participants as $p)
        @php
            $prog = $p->program;
            $statusColor = match($p->status) {
                'active'    => 'bg-green-100 text-green-800',
                'graduated' => 'bg-blue-100 text-blue-800',
                default     => 'bg-red-100 text-red-800',
            };
            $progColor = match($prog?->status) {
                'ongoing'   => 'bg-green-100 text-green-800',
                'planned'   => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-gray-100 text-gray-800',
                default     => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <a href="{{ route('participant.program.show', $p) }}"
            class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-blue-400 hover:shadow-md transition block">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-semibold text-gray-900 text-base">
                    {{ $prog?->masterProgram?->name ?? 'Program Tidak Diketahui' }}
                </h3>
                <span class="px-2 py-0.5 text-xs rounded-full {{ $statusColor }} ml-2 whitespace-nowrap">
                    {{ ucfirst($p->status) }}
                </span>
            </div>
            <dl class="space-y-1.5 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Angkatan</dt>
                    <dd class="font-medium text-gray-800">{{ $prog?->angkatan ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Batch</dt>
                    <dd class="font-medium text-gray-800">
                        {{ \App\Helpers\Roman::convert((int)($prog?->paketPelatihan?->batch ?? $prog?->paketPelatihan?->code ?? 0)) }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Periode</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $prog?->start_date?->format('d/m/Y') ?? '-' }} –
                        {{ $prog?->end_date?->format('d/m/Y') ?? '-' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Status Program</dt>
                    <dd>
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $progColor }}">
                            {{ ucfirst($prog?->status ?? '-') }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Instruktur</dt>
                    <dd class="font-medium text-gray-800">
                        {{ $prog?->instructor?->name ?? 'Belum ditentukan' }}
                    </dd>
                </div>
            </dl>
            <div class="mt-4 text-right text-xs text-blue-600 font-medium">Lihat Detail →</div>
        </a>
        @endforeach
    </div>
    @endif
</div>
@endsection
@extends('layouts.participant')

@section('title', 'Jadwal Pelatihan')

@section('content')
@if($program)
<div class="bg-white rounded-lg shadow-sm border p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Jadwal Pelatihan</h2>
    
    <dl class="space-y-4">
        <div class="flex justify-between border-b pb-3">
            <dt class="font-medium text-gray-700">Program:</dt>
            <dd class="text-gray-900">{{ $program->masterProgram->name }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="font-medium text-gray-700">Batch:</dt>
            <dd class="text-gray-900">{{ $program->batch }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="font-medium text-gray-700">Tanggal Mulai:</dt>
            <dd class="text-gray-900">{{ $program->start_date->format('d F Y') }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="font-medium text-gray-700">Tanggal Selesai:</dt>
            <dd class="text-gray-900">{{ $program->end_date->format('d F Y') }}</dd>
        </div>
        <div class="flex justify-between border-b pb-3">
            <dt class="font-medium text-gray-700">Durasi:</dt>
            <dd class="text-gray-900">{{ $program->masterProgram->duration_hours }} jam</dd>
        </div>
        <div class="flex justify-between">
            <dt class="font-medium text-gray-700">Status:</dt>
            <dd>
                <span class="px-3 py-1 text-sm rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($program->status) }}
                </span>
            </dd>
        </div>
    </dl>
</div>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-12 text-center">
    <p class="text-gray-700">{{ $message ?? 'Jadwal belum tersedia' }}</p>
</div>
@endif
@endsection
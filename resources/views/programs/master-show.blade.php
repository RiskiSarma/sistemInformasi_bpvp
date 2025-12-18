{{-- resources/views/programs/master-show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Master Program')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.programs.master') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.master.edit', $masterProgram) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Master Program
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $masterProgram->name }}</h2>
                <p class="text-gray-600 mt-1">Kode: <span class="font-mono font-semibold">{{ $masterProgram->code }}</span></p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm {{ $masterProgram->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $masterProgram->is_active ? 'Aktif' : 'Tidak Aktif' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Durasi Pelatihan</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->duration_hours }} Jam</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Total Unit Kompetensi</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->competencyUnits->count() }} Unit</p>
            </div>
        </div>

        @if($masterProgram->description)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h3>
            <p class="text-gray-700 leading-relaxed">{{ $masterProgram->description }}</p>
        </div>
        @endif
        <div class="mt-6 pt-6 border-t">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $masterProgram->creator?->name ?? 'Sistem' }}
                        <span class="text-gray-500 text-xs">
                            ({{ $masterProgram->created_at->format('d M Y H:i') }})
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $masterProgram->updater?->name ?? 'Sistem' }}
                        <span class="text-gray-500 text-xs">
                            ({{ $masterProgram->updated_at->format('d M Y H:i') }})
                        </span>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Unit Kompetensi -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Unit Kompetensi</h3>
        </div>
        @if($masterProgram->competencyUnits->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($masterProgram->competencyUnits as $index => $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm font-medium text-gray-900">{{ $unit->code }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $unit->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($unit->description ?? '-', 60) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center text-gray-500">
            Belum ada unit kompetensi
        </div>
        @endif
    </div>

    <!-- Program yang Menggunakan -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Program yang Menggunakan</h3>
        </div>
        @if($masterProgram->programs->count() > 0)
        <div class="divide-y">
            @foreach($masterProgram->programs as $program)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $program->batch }}</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>
                
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center text-gray-500">
            Belum ada program yang menggunakan master ini
        </div>
        @endif
    </div>
</div>
@endsection


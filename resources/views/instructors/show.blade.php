@extends('layouts.app')

@section('title', 'Detail Instruktur')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.instructors.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.instructors.edit', $instructor) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit Data</span>
            </a>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start space-x-6">
            <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-3xl font-bold text-purple-600">{{ substr($instructor->name, 0, 1) }}</span>
            </div>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800">{{ $instructor->user->name }}</h2>
                <p class="text-lg text-purple-600 mt-1">{{ $instructor->expertise }}</p>
                <div class="mt-3 space-y-1">
                    <p class="text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $instructor->user->email }}
                    </p>
                    <p class="text-gray-600 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $instructor->phone }}
                    </p>
                </div>
                <span class="mt-4 inline-block px-3 py-1 text-sm rounded-full {{ $instructor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $instructor->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Detail -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Detail</h3>
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Pendidikan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $instructor->education ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Pengalaman</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $instructor->experience_years ? $instructor->experience_years . ' tahun' : '-' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Bergabung Sejak</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $instructor->created_at->format('d F Y') }}</dd>
                </div>
            </dl>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $instructor->creator?->name ?? 'Sistem' }}
                    <span class="text-gray-500 text-xs">
                        ({{ $instructor->created_at->format('d M Y H:i') }})
                    </span>
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui Oleh</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $instructor->updater?->name ?? 'Sistem' }}
                    <span class="text-gray-500 text-xs">
                        ({{ $instructor->updated_at->format('d M Y H:i') }})
                    </span>
                </dd>
            </div>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistik</h3>
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                    <div class="text-sm text-blue-600 mb-1">Total Program (dari Jadwal Aktif)</div>
                    <div class="text-3xl font-bold text-blue-700">{{ $totalPrograms }}</div>
                </div>
                <div class="p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                    <div class="text-sm text-green-600 mb-1">Sedang Berjalan</div>
                    <div class="text-3xl font-bold text-green-700">{{ $ongoingPrograms }}</div>
                </div>
                <div class="p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                    <div class="text-sm text-purple-600 mb-1">Total Peserta</div>
                    <div class="text-3xl font-bold text-purple-700">
                        {{ $instructor->programs->sum(fn($p) => $p->participants->count()) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs List -->
    @if($instructor->programs->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Program yang Diampu</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($instructor->programs as $program)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $program->masterProgram->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $program->batch }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $program->participants->count() }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($program->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500">Instruktur belum mengampu program apapun</p>
    </div>
    @endif
</div>
@endsection
{{-- resources/views/programs/units-show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Unit Kompetensi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.programs.units') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.units.edit', $unit) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Unit Kompetensi
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $unit->name }}</h2>
            <p class="text-gray-600 mt-1">Kode: <span class="font-mono font-semibold">{{ $unit->code }}</span></p>
        </div>

        <div class="space-y-6">
            <!-- Program -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Program Pelatihan</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $unit->masterProgram->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $unit->masterProgram->code ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            @if($unit->description)
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h3>
                <div class="bg-gray-50 border rounded-lg p-4">
                    <p class="text-gray-700 leading-relaxed">{{ $unit->description }}</p>
                </div>
            </div>
            @else
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h3>
                <p class="text-gray-500 italic">Tidak ada deskripsi</p>
            </div>
            @endif

            <!-- Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Dibuat Pada</h3>
                    <p class="text-gray-800">{{ $unit->created_at->format('d F Y, H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-1">Terakhir Diperbarui</h3>
                    <p class="text-gray-800">{{ $unit->updated_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between bg-white rounded-lg shadow-sm border p-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">Kelola Unit Kompetensi</h3>
            <p class="text-sm text-gray-600 mt-1">Edit atau hapus unit kompetensi ini</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.units.edit', $unit) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('admin.programs.units.destroy', $unit) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus unit kompetensi ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


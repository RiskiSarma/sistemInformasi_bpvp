@extends('layouts.app')

@section('title', 'Program Pelatihan (Master)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Program Pelatihan (Master)</h2>
            <p class="text-gray-600 mt-1">Kelola master data program pelatihan</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.programs.sync-kemnaker') }}" 
               class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync dari Kemnaker
            </a>
        </div>
    </div>

    <!-- Form Tambah Master Program -->
    <div class="bg-white rounded-lg shadow-sm border p-6" x-data="{ showForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Master Program</h3>
            <button @click="showForm = !showForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <span x-show="!showForm">+ Tambah Baru</span>
                <span x-show="showForm">× Tutup</span>
            </button>
        </div>

        <form x-show="showForm" x-collapse method="POST" action="{{ route('admin.programs.master.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Program <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kejuruan_id" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
                    <select name="kejuruan_id" id="kejuruan_id" required 
                            class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kejuruan --</option>
                        @foreach(App\Models\Kejuruan::all() as $kejuruan)
                            <option value="{{ $kejuruan->id }}" {{ old('kejuruan_id') == $kejuruan->id ? 'selected' : '' }}>
                                {{ $kejuruan->kejuruan }}
                            </option>
                        @endforeach
                    </select>
                    @error('kejuruan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bidang_pelatihan_id" class="block text-sm font-medium text-gray-700 mb-1">Bidang Pelatihan <span class="text-red-500">*</span></label>
                    <select name="bidang_pelatihan_id" id="bidang_pelatihan_id" required 
                            class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach(App\Models\BidangPelatihan::all() as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_pelatihan_id') == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->bidang_pelatihan }}
                            </option>
                        @endforeach
                    </select>
                    @error('bidang_pelatihan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours') }}" required min="1" 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('duration_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="versi" class="block text-sm font-medium text-gray-700 mb-1">Versi</label>
                    <input type="number" name="versi" id="versi" value="{{ old('versi', 1) }}" min="1" 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Efektif</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $masterProgram->tanggal ?? '') }}" 
                        class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_program" class="block text-sm font-medium text-gray-700 mb-1">File Program (PDF/Doc)</label>
                    <input type="file" name="file_program" id="file_program" accept=".pdf,.doc,.docx" 
                        class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @if(isset($masterProgram) && $masterProgram->file_program)
                        <p class="mt-2 text-sm text-gray-600">
                            File saat ini: <a href="{{ Storage::url($masterProgram->file_program) }}" target="_blank" class="text-blue-600 hover:underline">Lihat File</a>
                        </p>
                    @endif
                    @error('file_program')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3" 
                          class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Program Aktif</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <button type="button" @click="showForm = false" 
                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Master Program
                </button>
            </div>
        </form>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex items-center space-x-4">
            <input type="text" name="search" placeholder="Cari program..." value="{{ request('search') }}" 
                   class="flex-1 px-4 py-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Master Programs List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        {{-- <div class="overflow-x-hidden"> --}}
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kejuruan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($masterPrograms as $mp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium truncate max-w-[200px]">{{ $mp->code }}</td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $mp->name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $mp->kejuruan->kejuruan ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $mp->bidangPelatihan->bidang_pelatihan ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $mp->duration_hours }} jam</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $mp->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $mp->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.programs.master.show', $mp) }}" class="text-blue-600 hover:text-blue-800" title="Detail & Kelola Units">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.programs.master.edit', $mp) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.programs.master.destroy', $mp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus master program ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            Belum ada master program
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        {{-- </div> --}}

        @if($masterPrograms->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $masterPrograms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
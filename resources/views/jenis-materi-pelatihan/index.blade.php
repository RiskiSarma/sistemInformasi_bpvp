@extends('layouts.app')

@section('title', 'Jenis Materi Pelatihan')

@section('content')
<div class="space-y-6" x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    deleteModalOpen: false,
    formData: {}
}">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Jenis Materi Pelatihan</h2>
            <p class="text-gray-600 mt-1">Master data jenis materi yang diajarkan</p>
        </div>
        <button @click="addModalOpen = true" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Jenis Materi</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-16">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Materi Pelatihan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($jenisMateri as $jenis)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        {{ $jenis->jenis_materi_pelatihan }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $jenis->user->name ?? 'Sistem' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $jenis->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center space-x-3">
                            <button @click="
                                formData = {
                                    id: '{{ $jenis->id }}',
                                    jenis_materi_pelatihan: '{{ addslashes($jenis->jenis_materi_pelatihan) }}'
                                };
                                editModalOpen = true;
                            " 
                            class="text-green-600 hover:text-green-800 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="
                                formData = {
                                    id: '{{ $jenis->id }}',
                                    jenis_materi_pelatihan: '{{ addslashes($jenis->jenis_materi_pelatihan) }}'
                                };
                                deleteModalOpen = true;
                            " 
                            class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada data jenis materi pelatihan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL ADD --}}
    {{-- ========================================= --}}
    <div x-show="addModalOpen" 
         style="display: none" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="addModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Jenis Materi Pelatihan</h3>
                    <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('admin.jenis-materi-pelatihan.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Materi Pelatihan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="jenis_materi_pelatihan" 
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('jenis_materi_pelatihan') border-red-500 @enderror"
                               placeholder="Contoh: Teori, Praktik, Simulasi"
                               value="{{ old('jenis_materi_pelatihan') }}">
                        @error('jenis_materi_pelatihan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" 
                                @click="addModalOpen = false" 
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT --}}
    {{-- ========================================= --}}
    <div x-show="editModalOpen" 
         style="display: none" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="editModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Jenis Materi Pelatihan</h3>
                    <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'{{ route('admin.jenis-materi-pelatihan.index') }}/' + formData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Materi Pelatihan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="jenis_materi_pelatihan" 
                               required
                               :value="formData.jenis_materi_pelatihan"
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" 
                                @click="editModalOpen = false" 
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL DELETE --}}
    {{-- ========================================= --}}
    <div x-show="deleteModalOpen" 
         style="display: none" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="deleteModalOpen = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-lg max-w-md w-full p-6 shadow-xl">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Hapus Jenis Materi?</h3>
                <p class="text-sm text-gray-600 text-center mb-1">Anda akan menghapus:</p>
                <p class="text-sm font-semibold text-gray-900 text-center mb-4" x-text="formData.jenis_materi_pelatihan"></p>
                <p class="text-sm text-gray-600 text-center mb-6">Aksi ini tidak dapat dibatalkan!</p>
                
                <form :action="'{{ route('admin.jenis-materi-pelatihan.index') }}/' + formData.id" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center space-x-3">
                        <button type="button" 
                                @click="deleteModalOpen = false" 
                                class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Auto-open modal jika ada error validasi --}}
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(old('jenis_materi_pelatihan'))
        // Trigger Alpine.js untuk buka modal
        window.dispatchEvent(new CustomEvent('alpine:init'));
        @endif
    });
</script>
@endif
@endsection
@extends('layouts.app')

@section('title', 'Detail SKKNI & Unit Kompetensi')

@section('content')
<div class="space-y-6"
x-data="{
    showAddUnitModal: false,
    showEditUnitModal: false,
    showDeleteUnitModal: false,
    showViewUnitModal: false,
    selectedUnit: null,
    deleteUnitId: null,

    openEditUnit(unit) {
        this.selectedUnit = unit;
        this.showEditUnitModal = true;
    },

    openViewUnit(unit) {
        this.selectedUnit = unit;
        this.showViewUnitModal = true;
    }
}">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.independent-units.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar SKKNI</span>
        </a>
    </div>

    <!-- SKKNI Info Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $skkni->skkni }}</h2>
                <p class="text-gray-600 mt-1">Nomor: <span class="font-mono font-semibold">{{ $skkni->nomor }}</span></p>
            </div>
            @if($skkni->berlaku == 'Y')
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Berlaku</span>
            @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Tidak Berlaku</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Tanggal</h4>
                <p class="text-gray-700">{{ $skkni->tanggal ? \Carbon\Carbon::parse($skkni->tanggal)->format('d F Y') : '-' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-1">Total Unit Kompetensi</h4>
                <p class="text-gray-700 font-semibold">{{ $skkni->independentUnits->count() }} unit</p>
            </div>
            <div class="md:col-span-2">
                <h4 class="text-sm font-medium text-gray-500 mb-2">Dokumen SKKNI</h4>

                @if($skkni->file_path)
                    @php
                        // Check if file is local (already downloaded) or remote (from Proglat)
                        $isLocal = str_starts_with($skkni->file_path, 'skkni/');
                        
                        if ($isLocal) {
                            // File lokal yang sudah didownload
                            $fileExists = Storage::disk('public')->exists($skkni->file_path);
                        } else {
                            // File remote dari Proglat - kita akan akses via proxy
                            $fileExists = true; // Assume exists (will be checked by proxy)
                        }
                    @endphp

                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="p-4 bg-white rounded-lg shadow-sm">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $skkni->file_name ?? 'Dokumen SKKNI.pdf' }}</p>
                                    @if($isLocal && $fileExists)
                                        <p class="text-sm text-green-600 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            File tersedia lokal
                                        </p>
                                    @else
                                        <p class="text-sm text-orange-600 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Remote dari server Proglat
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="{{ route('admin.independent-units.preview-file', $skkni) }}" target="_blank" 
                                   class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Preview
                                </a>

                                <a href="{{ route('admin.independent-units.download-file', $skkni) }}" 
                                   class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-blue-200 grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                            <div>
                                <span class="text-gray-600">Nama File:</span>
                                <p class="font-medium text-gray-900">{{ $skkni->file_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Tipe:</span>
                                <p class="font-medium text-gray-900">{{ $skkni->file_type ?? 'application/pdf' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Lokasi:</span>
                                <p class="font-medium text-gray-900">{{ $isLocal ? 'Server Lokal' : 'Server Proglat' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-50 border border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-600 font-medium">Tidak ada dokumen SKKNI</p>
                        <p class="text-sm text-gray-500 mt-2">File belum tersedia atau belum di-sync</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Unit Kompetensi Section (sama seperti sebelumnya) -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Unit Kompetensi</h3>
            <button @click="showAddUnitModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Tambah Unit</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Terkait</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($skkni->independentUnits as $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-gray-900">{{ $unit->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $unit->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">{{ $unit->description ? Str::limit($unit->description, 80) : '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-700">{{ $unit->programPelatihanUnits->count() }} program</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <button @click='openViewUnit({{ $unit->load("programPelatihanUnits.masterProgram")->toJson() }})' class="text-blue-600 hover:text-blue-800" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button @click='openEditUnit({{ $unit->toJson() }})' class="text-green-600 hover:text-green-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteUnitId = '{{ $unit->id }}'; showDeleteUnitModal = true" class="text-red-600 hover:text-red-800" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Belum ada unit kompetensi</p>
                                <p class="text-sm text-gray-400 mt-1">Klik "Tambah Unit" untuk membuat unit baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal View Unit Detail -->
    <div x-show="showViewUnitModal && selectedUnit" class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden" x-transition>
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showViewUnitModal = false"></div>
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Detail Unit Kompetensi</h3>
                <button @click="showViewUnitModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Kode Unit</h4>
                    <p class="text-gray-900 font-mono font-semibold" x-text="selectedUnit.code"></p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Nama Unit</h4>
                    <p class="text-gray-900 font-medium" x-text="selectedUnit.name"></p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h4>
                    <p class="text-gray-700" x-text="selectedUnit.description || 'Tidak ada deskripsi'"></p>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Program Terkait</h4>
                    <template x-if="selectedUnit.program_pelatihan_units && selectedUnit.program_pelatihan_units.length > 0">
                        <ul class="list-disc pl-5 space-y-1">
                            <template x-for="pivot in selectedUnit.program_pelatihan_units" :key="pivot.id">
                                <li class="text-gray-700">
                                    <span x-text="pivot.master_program ? pivot.master_program.name : 'Program Tidak Ditemukan'"></span>
                                    <span class="text-gray-500 text-sm" x-text="pivot.jp ? '(JP: ' + pivot.jp + ')' : ''"></span>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!selectedUnit.program_pelatihan_units || selectedUnit.program_pelatihan_units.length === 0">
                        <p class="text-gray-500 italic">Tidak ada program terkait</p>
                    </template>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Dibuat</h4>
                        <p class="text-sm text-gray-700" x-text="new Date(selectedUnit.created_at).toLocaleString('id-ID')"></p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Terakhir Diupdate</h4>
                        <p class="text-sm text-gray-700" x-text="new Date(selectedUnit.updated_at).toLocaleString('id-ID')"></p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-8">
                <button type="button" @click="showViewUnitModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Unit -->
    <div x-show="showAddUnitModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden" x-transition>
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showAddUnitModal = false"></div>
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Tambah Unit Kompetensi</h3>
                <button @click="showAddUnitModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.independent-units.store-unit', $skkni) }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Unit <span class="text-red-500">*</span></label>
                        <input type="text" name="code" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Unit <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Program Master (opsional)</label>
                    <select name="program_pelatihan_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Program (opsional) --</option>
                        @foreach($masterPrograms as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->code }} - {{ $mp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">JP (Jam Pelajaran)</label>
                    <input type="number" name="jp" min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" @click="showAddUnitModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan Unit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Unit -->
    <div x-show="showEditUnitModal && selectedUnit" class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden" x-transition>
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showEditUnitModal = false"></div>
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Edit Unit Kompetensi</h3>
                <button @click="showEditUnitModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" :action="`{{ route('admin.independent-units.index') }}/${selectedUnit.id}/update-unit`">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Unit <span class="text-red-500">*</span></label>
                        <input type="text" name="code" x-model="selectedUnit.code" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Unit <span class="text-red-500">*</span></label>
                        <input type="text" name="name" x-model="selectedUnit.name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="4" x-model="selectedUnit.description || ''" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" @click="showEditUnitModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showDeleteUnitModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-hidden" x-transition>
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showDeleteUnitModal = false"></div>
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Konfirmasi Hapus</h3>
                <button @click="showDeleteUnitModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <p class="text-gray-700 mb-6">Yakin ingin menghapus unit ini? Aksi tidak bisa dibatalkan.</p>

            <div class="flex items-center justify-end space-x-3">
                <button type="button" @click="showDeleteUnitModal = false" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <form :action="`{{ route('admin.independent-units.index') }}/${deleteUnitId}/delete-unit`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
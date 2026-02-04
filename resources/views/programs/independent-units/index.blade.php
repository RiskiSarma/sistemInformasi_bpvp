@extends('layouts.app')

@section('title', 'SKKNI Management')

@section('content')
<div class="space-y-6"
x-data="{
    showAddModal: false,
    showEditModal: false,
    showDeleteModal: false,
    showViewModal: false,
    
    selectedSkkni: null,
    deleteSkkniId: null,

    openEdit(skkni) {
        this.selectedSkkni = skkni;
        this.showEditModal = true;
    }
}">
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            {{-- <h2 class="text-2xl font-bold text-gray-800">SKKNI Management</h2> --}}
            <h2 class="text-2l font-bold text-gray-600 mt-1">Kelola SKKNI dan Unit Kompetensi dari Proglat</h2>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Menjadi ini (form POST) -->
            <form action="{{ route('admin.independent-units.sync-proglat') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center"
                        onclick="return confirm('Yakin ingin sync data SKKNI dari Proglat? Proses ini mungkin memakan waktu.');">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sync SKKNI dari Proglat
                </button>
            </form>
            <!-- Tambah Manual Button -->
                <button data-modal-target="addSkkniModal" data-modal-toggle="addSkkniModal"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2 shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Tambah SKKNI Manual</span>
                </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari SKKNI</label>
                <input type="text" name="search" placeholder="Nomor atau nama SKKNI..." value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="berlaku" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua</option>
                    <option value="Y" {{ request('berlaku') == 'Y' ? 'selected' : '' }}>Berlaku</option>
                    <option value="N" {{ request('berlaku') == 'N' ? 'selected' : '' }}>Tidak Berlaku</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- SKKNI List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor SKKNI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama SKKNI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Kompetensi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($skknis as $skkni)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $loop->iteration + ($skknis->currentPage() - 1) * $skknis->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-gray-900">{{ $skkni->nomor }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $skkni->skkni }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $skkni->tanggal ? \Carbon\Carbon::parse($skkni->tanggal)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($skkni->berlaku == 'Y')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Berlaku</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Tidak Berlaku</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-700">{{ $skkni->independentUnits->count() }} unit</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <!-- Lihat Detail -->
                                    <a href="{{ route('admin.independent-units.show', $skkni) }}" class="text-blue-600 hover:text-blue-800 transition" title="Lihat Detail & Unit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <button data-modal-target="editSkkniModal-{{ $skkni->id }}" data-modal-toggle="editSkkniModal-{{ $skkni->id }}" 
                                            class="text-green-600 hover:text-green-800 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Hapus -->
                                    <button data-modal-target="deleteSkkniModal-{{ $skkni->id }}" data-modal-toggle="deleteSkkniModal-{{ $skkni->id }}" 
                                            class="text-red-600 hover:text-red-800 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                            </div>
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Belum ada data SKKNI</p>
                                <p class="text-sm text-gray-400 mt-1">Klik "Sync SKKNI dari Proglat" untuk mengambil data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($skknis->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $skknis->links() }}
        </div>
        @endif
    </div>
</div>
    <!-- Modal Tambah SKKNI -->
    <div id="addSkkniModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl">
            <div class="relative bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Tambah SKKNI Manual</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="addSkkniModal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <form method="POST" action="{{ route('admin.independent-units.store-skkni') }}">
                        @csrf
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nomor SKKNI <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Contoh: Nomor 238 Tahun 2004">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nama SKKNI <span class="text-red-500">*</span></label>
                                <input type="text" name="skkni" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nama SKKNI">
                            </div>
                        </div>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                                <input type="date" name="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Status Berlaku <span class="text-red-500">*</span></label>
                                <select name="berlaku" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Y">Berlaku</option>
                                    <option value="N">Tidak Berlaku</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <button type="button" data-modal-hide="addSkkniModal" class="px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200">Batal</button>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan SKKNI</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit SKKNI -->
    @foreach($skknis as $skkni)
    <div id="editSkkniModal-{{ $skkni->id }}" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl">
            <div class="relative bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Edit SKKNI: {{ $skkni->nomor }}</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="editSkkniModal-{{ $skkni->id }}">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <form method="POST" action="{{ route('admin.independent-units.update-skkni', $skkni) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nomor SKKNI <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor" value="{{ $skkni->nomor }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Nama SKKNI <span class="text-red-500">*</span></label>
                                <input type="text" name="skkni" value="{{ $skkni->skkni }}" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                        </div>
                        <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                                <input type="date" name="tanggal" value="{{ $skkni->tanggal ? $skkni->tanggal->format('Y-m-d') : '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900">Status Berlaku <span class="text-red-500">*</span></label>
                                <select name="berlaku" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Y" {{ $skkni->berlaku == 'Y' ? 'selected' : '' }}>Berlaku</option>
                                    <option value="N" {{ $skkni->berlaku == 'N' ? 'selected' : '' }}>Tidak Berlaku</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                            <button type="button" data-modal-hide="editSkkniModal-{{ $skkni->id }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">Batal</button>
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Modal Konfirmasi Hapus -->
    @foreach($skknis as $skkni)
    <div id="deleteSkkniModal-{{ $skkni->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md">
            <div class="relative bg-white rounded-lg shadow-xl">
                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center" data-modal-hide="deleteSkkniModal-{{ $skkni->id }}">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-6 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-14 h-14" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Yakin ingin menghapus SKKNI <strong>{{ $skkni->nomor }}</strong>?</h3>
                    <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">Semua unit kompetensi di bawahnya juga akan terhapus. Aksi ini tidak bisa dibatalkan.</p>
                    <form action="{{ route('admin.independent-units.delete-skkni', $skkni) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button data-modal-hide="deleteSkkniModal-{{ $skkni->id }}" type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                            Ya, Hapus
                        </button>
                    </form>
                    <button data-modal-hide="deleteSkkniModal-{{ $skkni->id }}" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
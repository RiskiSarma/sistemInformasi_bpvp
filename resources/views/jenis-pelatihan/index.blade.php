@extends('layouts.app')

@section('title', 'Kelola Jenis Pelatihan')

@section('content')
<div class="space-y-6" x-data="{
    addModalOpen: false,
    editModalOpen: false,
    deleteModalOpen: false,
    deleteId: null,
    editId: null,
    editNama: '',
}">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Jenis Pelatihan</h2>
        <button @click="addModalOpen = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Jenis</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pelatihan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($jenis as $j)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $j->jenis_pelatihan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $j->user?->name ?? 'Sistem' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $j->updated_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $j->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-4">
                        <!-- Edit -->
                        <button @click="editId = {{ $j->id }}; editNama = '{{ addslashes($j->jenis_pelatihan) }}'; editModalOpen = true" class="text-blue-600 hover:text-blue-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>

                        <!-- Hapus -->
                        <button @click="deleteId = {{ $j->id }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-500 bg-gray-50">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-lg font-medium">Belum ada jenis pelatihan</p>
                        <p class="text-sm mt-2">Klik tombol Tambah Jenis untuk memulai</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($jenis->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $jenis->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah -->
    <div x-show="addModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-add" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="addModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-add">Tambah Jenis Pelatihan Baru</h3>
                            <div class="mt-6">
                                <form action="{{ route('admin.programs.jenis-pelatihan.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan *</label>
                                        <select name="jenis_pelatihan" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="" disabled selected>-- Pilih Jenis --</option>
                                            <option value="Non Boarding">Non Boarding</option>
                                            <option value="Boarding">Boarding</option>
                                            <option value="Project Based Learning (PBL)">Project Based Learning (PBL)</option>
                                            <option value="Tailor Made Training">Tailor Made Training</option>
                                            <option value="PFLK">PFLK</option>
                                        </select>
                                        @error('jenis_pelatihan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div x-show="editModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-edit">Edit Jenis Pelatihan</h3>
                            <div class="mt-6">
                                <form id="editJenisForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id" :value="editId">
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan *</label>
                                        <select name="jenis_pelatihan" required x-model="editNama" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="" disabled>-- Pilih Jenis --</option>
                                            <option value="Non Boarding">Non Boarding</option>
                                            <option value="Boarding">Boarding</option>
                                            <option value="Project Based Learning (PBL)">Project Based Learning (PBL)</option>
                                            <option value="Tailor Made Training">Tailor Made Training</option>
                                            <option value="PFLK">PFLK</option>
                                        </select>
                                        @error('jenis_pelatihan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Batal</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Hapus (Konfirmasi) -->
    <div x-show="deleteModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="deleteModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-delete">Hapus Jenis Pelatihan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">Apakah kamu yakin ingin menghapus jenis pelatihan ini? Aksi ini tidak bisa dibatalkan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                        <button type="button" @click="deleteModalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">Batal</button>
                        <form :action="'{{ route('admin.programs.jenis-pelatihan.destroy', ':id') }}'.replace(':id', deleteId)" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-1 sm:text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk set action form edit -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('jenisPelatihan', () => ({
        addModalOpen: false,
        editModalOpen: false,
        deleteModalOpen: false,
        deleteId: null,
        editId: null,
        editNama: '',

        editJenis(id, nama) {
            this.editId = id;
            this.editNama = nama.trim();
            this.editModalOpen = true;

            // Set action form edit secara dinamis
            const form = document.getElementById('editJenisForm');
            form.action = '{{ route("admin.programs.jenis-pelatihan.update", ":id") }}'.replace(':id', id);
        }
    }));
});
</script>
@endsection
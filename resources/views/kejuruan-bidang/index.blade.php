@extends('layouts.app')

@section('title', 'Kejuruan & Bidang')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{
    tab: 'kejuruan',
    modalOpen: false,
    modalType: '',
    deleteModalOpen: false,
    deleteItem: null,
    deleteType: '',
    syncModalOpen: false,
    currentItem: {},
    formData: {
        code: '',
        kejuruan: '',
        kejuruan_id: '',
        bidang_pelatihan: '',
        deskripsi: ''
    },
    openModal(type, item = {}) {
        this.modalType = type;
        this.currentItem = item;
        this.formData = {
            code: item.code || '',
            kejuruan: item.kejuruan || '',
            kejuruan_id: item.kejuruan_id || '',
            bidang_pelatihan: item.bidang_pelatihan || '',
            deskripsi: item.deskripsi || ''
        };
        this.modalOpen = true;
    },
    openDeleteModal(type, item) {
        this.deleteType = type;
        this.deleteItem = item;
        this.deleteModalOpen = true;
    },
    closeModal() {
        this.modalOpen = false;
        this.formData = { code: '', kejuruan: '', kejuruan_id: '', bidang_pelatihan: '', deskripsi: '' };
    },
    closeDeleteModal() {
        this.deleteModalOpen = false;
        this.deleteItem = null;
        this.deleteType = '';
    },
    openSyncModal() {
        this.syncModalOpen = true;
    },
    closeSyncModal() {
        this.syncModalOpen = false;
    },
    confirmSync() {
        document.getElementById('sync-form').submit();
        this.closeSyncModal();
    }
}">
    <h1 class="text-2xl font-bold mb-6">Kejuruan dan Bidang Pelatihan</h1>

    <div class="bg-white shadow rounded-lg">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex">
                <button @click="tab = 'kejuruan'" 
                        :class="tab === 'kejuruan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex-1 text-center">
                    Kejuruan
                </button>
                <button @click="tab = 'bidang'" 
                        :class="tab === 'bidang' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex-1 text-center">
                    Bidang Pelatihan
                </button>
            </nav>
        </div>

        <!-- Tab Kejuruan -->
        <div x-show="tab === 'kejuruan'" class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Daftar Kejuruan</h2>
                <div class="flex items-center space-x-3">
                    <!-- Tombol Sync → Buka Modal -->
                    <button @click="openSyncModal()"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2 shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Sync dari Proglat</span>
                    </button>

                    <!-- Tambah Kejuruan -->
                    <button @click="openModal('addKejuruan')" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition transform hover:scale-105 shadow-md">
                        + Tambah Kejuruan
                    </button>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kejuruan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Pada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate Pada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kejuruans as $kejuruan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ $kejuruan->code ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $kejuruan->kejuruan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kejuruan->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $kejuruan->updated_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-3">
                            <button @click="openModal('editKejuruan', {{ json_encode($kejuruan) }})" 
                                    class="text-blue-600 hover:text-blue-900 transition hover:underline" title="Edit Kejuruan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="openDeleteModal('kejuruan', {{ json_encode($kejuruan) }})" 
                                    class="text-red-600 hover:text-red-900 transition hover:underline" title="Hapus Kejuruan">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Belum ada data kejuruan</p>
                                <p class="text-sm text-gray-400 mt-1">Klik "Sync dari Proglat" atau "Tambah Kejuruan" untuk menambah data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Tab Bidang Pelatihan -->
        <div x-show="tab === 'bidang'" class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Daftar Bidang Pelatihan</h2>
                <button @click="openModal('addBidang')" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition transform hover:scale-105 shadow-md">
                    + Tambah Bidang
                </button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bidang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Pada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate Pada</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bidangs as $bidang)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $bidang->bidang_pelatihan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $bidang->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $bidang->updated_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-3">
                            <button @click="openModal('editBidang', {{ json_encode($bidang) }})" 
                                    class="text-blue-600 hover:text-blue-900 transition hover:underline" title="Edit Bidang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button @click="openDeleteModal('bidang', {{ json_encode($bidang) }})" 
                                    class="text-red-600 hover:text-red-900 transition hover:underline" title="Hapus Bidang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">Belum ada data bidang pelatihan</p>
                                <p class="text-sm text-gray-400 mt-1">Klik "Tambah Bidang" untuk menambah data</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modalOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-300">
                
                <div class="bg-white px-8 pt-8 pb-10 sm:p-10">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                            <span x-text="modalType.includes('Kejuruan') ? 'Form Kejuruan' : 'Form Bidang Pelatihan'"></span>
                        </h3>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition transform hover:scale-110 focus:outline-none">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Form Kejuruan -->
                    <template x-if="modalType === 'addKejuruan' || modalType === 'editKejuruan'">
                        <form method="POST" 
                              :action="modalType === 'addKejuruan' 
                                      ? '{{ route('admin.programs.kejuruan-bidang.kejuruan.store') }}' 
                                      : '{{ route('admin.programs.kejuruan-bidang.kejuruan.update', ['kejuruan' => 0]) }}'.replace('/0', '/' + currentItem.id)">
                            @csrf
                            <input type="hidden" name="_method" x-bind:value="modalType === 'editKejuruan' ? 'PUT' : ''">

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-base font-medium text-gray-800 mb-2">Kode Kejuruan <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" x-model="formData.code" required 
                                           placeholder="Contoh: PAR, TI, TEK"
                                           maxlength="10"
                                           class="block w-full rounded-xl border-2 border-gray-400 px-5 py-4 text-lg shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none transition duration-200 uppercase">
                                    <p class="text-sm text-gray-500 mt-1">Maksimal 10 karakter. Contoh: PAR (Pariwisata), TI (Teknologi Informasi)</p>
                                </div>
                                <div>
                                    <label class="block text-base font-medium text-gray-800 mb-2">Nama Kejuruan <span class="text-red-500">*</span></label>
                                    <input type="text" name="kejuruan" x-model="formData.kejuruan" required 
                                           placeholder="Contoh: Pariwisata"
                                           class="block w-full rounded-xl border-2 border-gray-400 px-5 py-4 text-lg shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none transition duration-200">
                                </div>
                            </div>

                            <div class="mt-10 flex justify-end space-x-5">
                                <button type="button" @click="closeModal" 
                                        class="px-8 py-4 bg-gray-200 text-gray-800 text-lg font-medium rounded-xl hover:bg-gray-300 transition duration-200 transform hover:scale-105 focus:outline-none">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-xl hover:bg-blue-700 transition duration-200 transform hover:scale-105 focus:outline-none">
                                    <span x-text="modalType === 'addKejuruan' ? 'Tambah' : 'Update'"></span>
                                </button>
                            </div>
                        </form>
                    </template>

                    <!-- Form Bidang -->
                    <template x-if="modalType === 'addBidang' || modalType === 'editBidang'">
                        <form method="POST" 
                              :action="modalType === 'addBidang' 
                                      ? '{{ route('admin.programs.kejuruan-bidang.bidang.store') }}' 
                                      : '{{ route('admin.programs.kejuruan-bidang.bidang.update', ['bidang' => 0]) }}'.replace('/0', '/' + currentItem.id)">
                            @csrf
                            <input type="hidden" name="_method" x-bind:value="modalType === 'editBidang' ? 'PUT' : ''">

                            <div class="space-y-8">
                                <div>
                                    <label class="block text-base font-medium text-gray-800 mb-2">Nama Bidang <span class="text-red-500">*</span></label>
                                    <input type="text" name="bidang_pelatihan" x-model="formData.bidang_pelatihan" required 
                                           class="block w-full rounded-xl border-2 border-gray-400 px-5 py-4 text-lg shadow-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none transition duration-200">
                                </div>
                            </div>

                            <div class="mt-10 flex justify-end space-x-5">
                                <button type="button" @click="closeModal" 
                                        class="px-8 py-4 bg-gray-200 text-gray-800 text-lg font-medium rounded-xl hover:bg-gray-300 transition duration-200 transform hover:scale-105 focus:outline-none">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-xl hover:bg-blue-700 transition duration-200 transform hover:scale-105 focus:outline-none">
                                    <span x-text="modalType === 'addBidang' ? 'Tambah' : 'Update'"></span>
                                </button>
                            </div>
                        </form>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" role="dialog" aria-modal="true"
         style="display: none;">
        
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="deleteModalOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-300">
                
                <div class="bg-white px-8 py-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="delete-modal-title">
                        Konfirmasi Hapus
                    </h3>

                    <p class="text-lg text-gray-700 mb-8">
                        Apakah Anda yakin ingin menghapus 
                        <span x-text="deleteType === 'kejuruan' ? 'kejuruan' : 'bidang pelatihan'"></span> 
                        ini? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div class="flex justify-end space-x-5">
                        <button @click="closeDeleteModal" 
                                class="px-8 py-4 bg-gray-200 text-gray-800 text-lg font-medium rounded-xl hover:bg-gray-300 transition duration-200 transform hover:scale-105 focus:outline-none">
                            Batal
                        </button>
                        <form method="POST" 
                              :action="deleteType === 'kejuruan' 
                                      ? '{{ route('admin.programs.kejuruan-bidang.kejuruan.destroy', '') }}/' + deleteItem.id 
                                      : '{{ route('admin.programs.kejuruan-bidang.bidang.destroy', '') }}/' + deleteItem.id">
                            @csrf @method('DELETE')
                            <button type="submit" 
                                    class="px-8 py-4 bg-red-600 text-white text-lg font-medium rounded-xl hover:bg-red-700 transition duration-200 transform hover:scale-105 focus:outline-none">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal Konfirmasi Sync (INI YANG BARU & PENTING) -->
    <div x-show="syncModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="sync-modal-title" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="syncModalOpen"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-300">
                
                <div class="bg-white px-8 py-10">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="sync-modal-title">
                        Konfirmasi Sinkronisasi
                    </h3>

                    <p class="text-lg text-gray-700 mb-8">
                        Anda akan menyinkronkan data Kejuruan dari Proglat Kemnaker.<br>
                        Proses ini bisa memakan waktu beberapa detik hingga menit tergantung jumlah data.<br><br>
                        <strong>Yakin ingin melanjutkan?</strong>
                    </p>

                    <div class="flex justify-end space-x-5">
                        <button @click="closeSyncModal" 
                                class="px-8 py-4 bg-gray-200 text-gray-800 text-lg font-medium rounded-xl hover:bg-gray-300 transition duration-200 transform hover:scale-105 focus:outline-none">
                            Batal
                        </button>
                        
                        <form id="sync-form" action="{{ route('admin.programs.kejuruan-bidang.sync-kejuruan') }}" method="POST" class="inline">
                            @csrf
                            <button type="button" @click="confirmSync"
                                    class="px-8 py-4 bg-green-600 text-white text-lg font-medium rounded-xl hover:bg-green-700 transition duration-200 transform hover:scale-105 focus:outline-none flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span>Ya, Sync Sekarang</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('title', 'Kelola Paket Pelatihan')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false, editModalOpen: false, deleteModalOpen: false, deleteId: null }">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Paket Pelatihan</h2>
        <button @click="addModalOpen = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Paket</span>
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto"> <!-- Ini kunci biar responsive tanpa scroll page, tapi table bisa scroll kalau screen kecil -->
            <table class="w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="w-20 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pelatihan</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="w-28 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JP Harian</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JP Industri</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sabtu</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate</th>
                        <th class="w-28y px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pakets as $paket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-4 text-sm">{{ $paket->jenisPelatihan->jenis_pelatihan ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm">{{ $paket->tahun }}</td>
                        <td class="px-4 py-4 text-sm font-medium">
                            @if($paket->batch)
                                Batch {{ \App\Helpers\Roman::convert($paket->batch) }} <!-- Romawi: Batch I, Batch II, dll -->
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm">{{ $paket->jp_harian ? $paket->jp_harian . ' jam' : '-' }}</td>
                        <td class="px-4 py-4 text-sm">{{ $paket->jp_industri ? $paket->jp_industri . ' jam' : '-' }}</td>
                        <td class="px-4 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $paket->sabtu_masuk == 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $paket->sabtu_masuk == 'Y' ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $paket->minggu_masuk == 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $paket->minggu_masuk == 'Y' ? 'Ya' : 'Tidak' }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $paket->user->name ?? 'Sistem' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-500">{{ $paket->updated_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <button @click="editModalOpen = true; editPaket({{ $paket->id }}, {{ $paket->jenis_pelatihan_id }}), {{ $paket->tahun }}, {{ $paket->batch ?? 'null' }}, {{ $paket->jp_harian }}, {{ $paket->jp_industri }}, '{{ $paket->sabtu_masuk }}', '{{ $paket->minggu_masuk }}', '{{ $paket->tanggal_mulai ? $paket->tanggal_mulai->format('Y-m-d') : '' }}', '{{ $paket->tanggal_akhir ? $paket->tanggal_akhir->format('Y-m-d') : '' }}', '{{ $paket->tanggal_awal_pendaftaran ? $paket->tanggal_awal_pendaftaran->format('Y-m-d') : '' }}', '{{ $paket->tanggal_akhir_pendaftaran ? $paket->tanggal_akhir_pendaftaran->format('Y-m-d') : '' }}', '{{ $paket->tanggal_awal_tes_tulis ? $paket->tanggal_awal_tes_tulis->format('Y-m-d') : '' }}', '{{ $paket->tanggal_akhir_tes_tulis ? $paket->tanggal_akhir_tes_tulis->format('Y-m-d') : '' }}', '{{ $paket->tanggal_awal_wawancara ? $paket->tanggal_awal_wawancara->format('Y-m-d') : '' }}', '{{ $paket->tanggal_akhir_wawancara ? $paket->tanggal_akhir_wawancara->format('Y-m-d') : '' }}', '{{ $paket->tanggal_awal_daftar_ulang ? $paket->tanggal_awal_daftar_ulang->format('Y-m-d') : '' }}', '{{ $paket->tanggal_akhir_daftar_ulang ? $paket->tanggal_akhir_daftar_ulang->format('Y-m-d') : '' }}', '{{ $paket->tanggal_pengumuman ? $paket->tanggal_pengumuman->format('Y-m-d') : '' }}', {{ $paket->user_id_pengumuman ?? 'null' }})" class="text-blue-600 hover:text-blue-900 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button @click="deleteId = {{ $paket->id }}; deleteModalOpen = true" class="text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center text-gray-500 bg-gray-50">
                            <p class="text-lg font-medium">Belum ada paket pelatihan</p>
                            <p class="text-sm mt-2">Klik tombol Tambah Paket untuk memulai</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pakets->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $pakets->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Tambah Paket -->
    <div x-show="addModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-add" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="addModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-add">Tambah Paket Pelatihan Baru</h3>
                            <div class="mt-6">
                                <form action="{{ route('admin.programs.paket-pelatihan.store') }}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan *</label>
                                            <select name="jenis_pelatihan_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Pilih Jenis --</option>
                                                @foreach($jenisPelatihans as $jenis)
                                                    <option value="{{ $jenis->id }}">{{ $jenis->jenis_pelatihan }}</option>
                                                @endforeach
                                            </select>
                                            @error('jenis_pelatihan_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun *</label>
                                            <input type="number" name="tahun" required min="2000" max="2100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch</label>
                                            <input type="number" name="batch" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">JP Harian</label>
                                            <input type="number" name="jp_harian" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">JP Industri</label>
                                            <input type="number" name="jp_industri" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Sabtu Masuk</label>
                                            <select name="sabtu_masuk" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="N">Tidak</option>
                                                <option value="Y">Ya</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Minggu Masuk</label>
                                            <select name="minggu_masuk" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="N">Tidak</option>
                                                <option value="Y">Ya</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                                            <input type="date" name="tanggal_akhir" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <!-- Tambahkan field tanggal lain sesuai kebutuhan -->
                                    </div>

                                    <div class="mt-8 flex justify-end space-x-4">
                                        <button type="button" @click="addModalOpen = false" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Batal</button>
                                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan Paket</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Paket (mirip tambah, tapi dengan JS set value) -->
    <div x-show="editModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-edit">Edit Paket Pelatihan</h3>
                            <div class="mt-6">
                                <form id="editPaketForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="paket_id" id="editPaketId">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Pelatihan *</label>
                                            <select name="jenis_pelatihan_id" id="editJenisPelatihanId" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Pilih Jenis --</option>
                                                @foreach($jenisPelatihans as $jenis)
                                                    <option value="{{ $jenis->id }}">{{ $jenis->jenis_pelatihan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Sisanya sama seperti modal tambah, tapi dengan id untuk JS set value -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun *</label>
                                            <input type="number" name="tahun" id="editTahun" required min="2000" max="2100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <!-- Tambahkan id untuk field lain sesuai kebutuhan -->
                                    </div>

                                    <div class="mt-8 flex justify-end space-x-4">
                                        <button type="button" @click="editModalOpen = false" class="px-6 py-3 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">Batal</button>
                                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Update Paket</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="deleteModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="deleteModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-delete">Hapus Paket Pelatihan</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Apakah kamu yakin ingin menghapus paket ini? Aksi ini tidak bisa dibatalkan.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" @click="deleteModalOpen = false" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">Batal</button>
                    <form id="deleteForm" method="POST" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-1 sm:text-sm">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Edit & Hapus -->
    <script>
    function editPaket(id, jenisPelatihanId, tahun, batch, jpHarian, jpIndustri, sabtuMasuk, mingguMasuk, tglMulai, tglAkhir, tglAwalPendaftaran, tglAkhirPendaftaran, tglAwalTesTulis, tglAkhirTesTulis, tglAwalWawancara, tglAkhirWawancara, tglAwalDaftarUlang, tglAkhirDaftarUlang, tglPengumuman, userIdPengumuman) {
        document.getElementById('editPaketId').value = id;
        document.getElementById('editJenisPelatihanId').value = jenisPelatihanId;
        document.getElementById('editTahun').value = tahun;
        document.getElementById('editBatch').value = batch || '';
        document.getElementById('editJpHarian').value = jpHarian;
        document.getElementById('editJpIndustri').value = jpIndustri;
        document.getElementById('editSabtuMasuk').value = sabtuMasuk;
        document.getElementById('editMingguMasuk').value = mingguMasuk;
        document.getElementById('editTglMulai').value = tglMulai;
        document.getElementById('editTglAkhir').value = tglAkhir;
        document.getElementById('editTglAwalPendaftaran').value = tglAwalPendaftaran;
        document.getElementById('editTglAkhirPendaftaran').value = tglAkhirPendaftaran;
        document.getElementById('editTglAwalTesTulis').value = tglAwalTesTulis;
        document.getElementById('editTglAkhirTesTulis').value = tglAkhirTesTulis;
        document.getElementById('editTglAwalWawancara').value = tglAwalWawancara;
        document.getElementById('editTglAkhirWawancara').value = tglAkhirWawancara;
        document.getElementById('editTglAwalDaftarUlang').value = tglAwalDaftarUlang;
        document.getElementById('editTglAkhirDaftarUlang').value = tglAkhirDaftarUlang;
        document.getElementById('editTglPengumuman').value = tglPengumuman;
        document.getElementById('editUserIdPengumuman').value = userIdPengumuman || '';

        document.getElementById('editPaketForm').action = '{{ route("admin.programs.paket-pelatihan.update", ":id") }}'.replace(':id', id);
        document.querySelector('[x-data]').__x.$data.editModalOpen = true;
    }

    function confirmDelete(id) {
        document.getElementById('deleteForm').action = '{{ route("admin.programs.paket-pelatihan.destroy", ":id") }}'.replace(':id', id);
        document.querySelector('[x-data]').__x.$data.deleteModalOpen = true;
    }
    </script>
@endsection
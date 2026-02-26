@extends('layouts.app')
@section('title', 'Kelola Paket Pelatihan')
@section('content')
<div class="space-y-6" x-data="{
    addModalOpen: false,
    editModalOpen: false,
    deleteModalOpen: false,
    viewModalOpen: false,
    pivotModalOpen: false,
    pivotTab: 'units',
    deleteId: null,
    viewData: {},
    formData: {},
    currentPaketId: null,
    
    // Watcher untuk update dropdown saat paket berubah
    init() {
        this.$watch('currentPaketId', value => {
            if (value && typeof updateProgramDropdown === 'function') {
                updateProgramDropdown(value);
            }
        });
    }
}">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Paket Pelatihan</h2>
        <button @click="addModalOpen = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Paket</span>
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="w-20 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pelatihan</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                        <th class="w-28 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JP Harian</th>
                        <th class="w-24 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sabtu</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minggu</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                        <th class="w-12 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate</th>
                        <th class="w-28 px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                Batch {{ \App\Helpers\Roman::convert($paket->batch) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-4 text-sm">{{ $paket->jp_harian ? $paket->jp_harian . ' jam' : '-' }}</td>
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
                                <!-- VIEW -->
                                <button
                                    @click="
                                        viewData = {
                                            jenis: '{{ addslashes($paket->jenisPelatihan->jenis_pelatihan ?? '-') }}',
                                            tahun: '{{ $paket->tahun ?? '-' }}',
                                            batch: '{{ $paket->batch ? 'Batch ' . \App\Helpers\Roman::convert($paket->batch) : '-' }}',
                                            jp_harian: '{{ $paket->jp_harian ? $paket->jp_harian . ' jam' : '-' }}',
                                            sabtu_masuk: '{{ $paket->sabtu_masuk ?? 'N' }}',
                                            minggu_masuk: '{{ $paket->minggu_masuk ?? 'N' }}',
                                            tgl_mulai: '{{ $paket->tanggal_mulai ? $paket->tanggal_mulai->format('d/m/Y') : '-' }}',
                                            tgl_akhir: '{{ $paket->tanggal_akhir ? $paket->tanggal_akhir->format('d/m/Y') : '-' }}',
                                            tgl_awal_pendaftaran: '{{ $paket->tanggal_awal_pendaftaran ? $paket->tanggal_awal_pendaftaran->format('d/m/Y') : '-' }}',
                                            tgl_akhir_pendaftaran: '{{ $paket->tanggal_akhir_pendaftaran ? $paket->tanggal_akhir_pendaftaran->format('d/m/Y') : '-' }}',
                                            tgl_awal_tes_tulis: '{{ $paket->tanggal_awal_tes_tulis ? $paket->tanggal_awal_tes_tulis->format('d/m/Y') : '-' }}',
                                            tgl_akhir_tes_tulis: '{{ $paket->tanggal_akhir_tes_tulis ? $paket->tanggal_akhir_tes_tulis->format('d/m/Y') : '-' }}',
                                            tgl_awal_wawancara: '{{ $paket->tanggal_awal_wawancara ? $paket->tanggal_awal_wawancara->format('d/m/Y') : '-' }}',
                                            tgl_akhir_wawancara: '{{ $paket->tanggal_akhir_wawancara ? $paket->tanggal_akhir_wawancara->format('d/m/Y') : '-' }}',
                                            tgl_awal_daftar_ulang: '{{ $paket->tanggal_awal_daftar_ulang ? $paket->tanggal_awal_daftar_ulang->format('d/m/Y') : '-' }}',
                                            tgl_akhir_daftar_ulang: '{{ $paket->tanggal_akhir_daftar_ulang ? $paket->tanggal_akhir_daftar_ulang->format('d/m/Y') : '-' }}',
                                            tgl_pengumuman: '{{ $paket->tanggal_pengumuman ? $paket->tanggal_pengumuman->format('d/m/Y') : '-' }}',
                                            dibuat: '{{ addslashes($paket->user->name ?? 'Sistem') }}',
                                            update: '{{ $paket->updated_at->format('d M Y H:i') }}'
                                        };
                                        viewModalOpen = true
                                    "
                                    class="text-indigo-600 hover:text-indigo-800"
                                    title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- EDIT -->
                                <button 
                                    @click="
                                        formData = {
                                            id: '{{ $paket->id }}',
                                            jenis_pelatihan_id: {{ $paket->jenis_pelatihan_id }},
                                            tahun: {{ $paket->tahun }},
                                            batch: {{ $paket->batch ?? 'null' }},
                                            jp_harian: {{ $paket->jp_harian ?? 'null' }},
                                            sabtu_masuk: '{{ $paket->sabtu_masuk }}',
                                            minggu_masuk: '{{ $paket->minggu_masuk }}',
                                            tanggal_mulai: '{{ $paket->tanggal_mulai ? $paket->tanggal_mulai->format('Y-m-d') : '' }}',
                                            tanggal_akhir: '{{ $paket->tanggal_akhir ? $paket->tanggal_akhir->format('Y-m-d') : '' }}',
                                            tanggal_awal_pendaftaran: '{{ $paket->tanggal_awal_pendaftaran ? $paket->tanggal_awal_pendaftaran->format('Y-m-d') : '' }}',
                                            tanggal_akhir_pendaftaran: '{{ $paket->tanggal_akhir_pendaftaran ? $paket->tanggal_akhir_pendaftaran->format('Y-m-d') : '' }}',
                                            tanggal_awal_tes_tulis: '{{ $paket->tanggal_awal_tes_tulis ? $paket->tanggal_awal_tes_tulis->format('Y-m-d') : '' }}',
                                            tanggal_akhir_tes_tulis: '{{ $paket->tanggal_akhir_tes_tulis ? $paket->tanggal_akhir_tes_tulis->format('Y-m-d') : '' }}',
                                            tanggal_awal_wawancara: '{{ $paket->tanggal_awal_wawancara ? $paket->tanggal_awal_wawancara->format('Y-m-d') : '' }}',
                                            tanggal_akhir_wawancara: '{{ $paket->tanggal_akhir_wawancara ? $paket->tanggal_akhir_wawancara->format('Y-m-d') : '' }}',
                                            tanggal_awal_daftar_ulang: '{{ $paket->tanggal_awal_daftar_ulang ? $paket->tanggal_awal_daftar_ulang->format('Y-m-d') : '' }}',
                                            tanggal_akhir_daftar_ulang: '{{ $paket->tanggal_akhir_daftar_ulang ? $paket->tanggal_akhir_daftar_ulang->format('Y-m-d') : '' }}',
                                            tanggal_pengumuman: '{{ $paket->tanggal_pengumuman ? $paket->tanggal_pengumuman->format('Y-m-d') : '' }}'
                                        };
                                        document.getElementById('editPaketId').value = formData.id;
                                        document.getElementById('editJenisPelatihanId').value = formData.jenis_pelatihan_id;
                                        document.getElementById('editTahun').value = formData.tahun;
                                        document.getElementById('editBatch').value = formData.batch || '';
                                        document.getElementById('editJpHarian').value = formData.jp_harian || '';
                                        document.getElementById('editSabtuMasuk').value = formData.sabtu_masuk;
                                        document.getElementById('editMingguMasuk').value = formData.minggu_masuk;
                                        document.getElementById('editTglMulai').value = formData.tanggal_mulai;
                                        document.getElementById('editTglAkhir').value = formData.tanggal_akhir;
                                        document.getElementById('editTglAwalPendaftaran').value = formData.tanggal_awal_pendaftaran;
                                        document.getElementById('editTglAkhirPendaftaran').value = formData.tanggal_akhir_pendaftaran;
                                        document.getElementById('editTglAwalTesTulis').value = formData.tanggal_awal_tes_tulis;
                                        document.getElementById('editTglAkhirTesTulis').value = formData.tanggal_akhir_tes_tulis;
                                        document.getElementById('editTglAwalWawancara').value = formData.tanggal_awal_wawancara;
                                        document.getElementById('editTglAkhirWawancara').value = formData.tanggal_akhir_wawancara;
                                        document.getElementById('editTglAwalDaftarUlang').value = formData.tanggal_awal_daftar_ulang;
                                        document.getElementById('editTglAkhirDaftarUlang').value = formData.tanggal_akhir_daftar_ulang;
                                        document.getElementById('editTglPengumuman').value = formData.tanggal_pengumuman;
                                        document.getElementById('editPaketForm').action = '{{ route('admin.programs.paket-pelatihan.update', ':id') }}'.replace(':id', formData.id);
                                        editModalOpen = true;
                                    "
                                    class="text-blue-600 hover:text-blue-900 transition"
                                    title="Edit"
                                >
                                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- MANAGE PIVOT (NEW BUTTON) -->
                                <button 
                                    @click="currentPaketId = '{{ $paket->id }}'; pivotTab = 'programs'; pivotModalOpen = true;"
                                    class="text-purple-600 hover:text-purple-900 transition"
                                    title="Kelola Data Pivot">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </button>

                                <!-- DELETE -->
                                <button 
                                    @click="
                                        deleteId = '{{ $paket->id }}';
                                        document.getElementById('deleteForm').action = '{{ route('admin.programs.paket-pelatihan.destroy', ':id') }}'.replace(':id', deleteId);
                                        deleteModalOpen = true;
                                    "
                                    class="text-red-600 hover:text-red-900"
                                    title="Hapus"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center text-gray-500 bg-gray-50">
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

    <!-- MODAL ADD PAKET (SAMA SEPERTI SEBELUMNYA) -->
    <div x-show="addModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="addModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Tambah Paket Pelatihan Baru</h3>
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
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Pendaftaran</label>
                                            <input type="date" name="tanggal_awal_pendaftaran" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Pendaftaran</label>
                                            <input type="date" name="tanggal_akhir_pendaftaran" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Tes Tulis</label>
                                            <input type="date" name="tanggal_awal_tes_tulis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Tes Tulis</label>
                                            <input type="date" name="tanggal_akhir_tes_tulis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Wawancara</label>
                                            <input type="date" name="tanggal_awal_wawancara" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Wawancara</label>
                                            <input type="date" name="tanggal_akhir_wawancara" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Daftar Ulang</label>
                                            <input type="date" name="tanggal_awal_daftar_ulang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Daftar Ulang</label>
                                            <input type="date" name="tanggal_akhir_daftar_ulang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengumuman</label>
                                            <input type="date" name="tanggal_pengumuman" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
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

    <!-- MODAL EDIT PAKET -->
    <div x-show="editModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-edit" role="dialog" aria-modal="true" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="editModalOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-edit">Edit Paket Pelatihan</h3>
                            <div class="mt-6">
                                <form id="editPaketForm" method="POST" enctype="multipart/form-data">
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

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun *</label>
                                            <input type="number" name="tahun" id="editTahun" required min="2000" max="2100" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch</label>
                                            <input type="number" name="batch" id="editBatch" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">JP Harian</label>
                                            <input type="number" name="jp_harian" id="editJpHarian" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Sabtu Masuk</label>
                                            <select name="sabtu_masuk" id="editSabtuMasuk" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="N">Tidak</option>
                                                <option value="Y">Ya</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Minggu Masuk</label>
                                            <select name="minggu_masuk" id="editMingguMasuk" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                <option value="N">Tidak</option>
                                                <option value="Y">Ya</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                                            <input type="date" name="tanggal_mulai" id="editTglMulai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                                            <input type="date" name="tanggal_akhir" id="editTglAkhir" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Pendaftaran</label>
                                            <input type="date" name="tanggal_awal_pendaftaran" id="editTglAwalPendaftaran" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Pendaftaran</label>
                                            <input type="date" name="tanggal_akhir_pendaftaran" id="editTglAkhirPendaftaran" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Tes Tulis</label>
                                            <input type="date" name="tanggal_awal_tes_tulis" id="editTglAwalTesTulis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Tes Tulis</label>
                                            <input type="date" name="tanggal_akhir_tes_tulis" id="editTglAkhirTesTulis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Wawancara</label>
                                            <input type="date" name="tanggal_awal_wawancara" id="editTglAwalWawancara" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Wawancara</label>
                                            <input type="date" name="tanggal_akhir_wawancara" id="editTglAkhirWawancara" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Awal Daftar Ulang</label>
                                            <input type="date" name="tanggal_awal_daftar_ulang" id="editTglAwalDaftarUlang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Akhir Daftar Ulang</label>
                                            <input type="date" name="tanggal_akhir_daftar_ulang" id="editTglAkhirDaftarUlang" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pengumuman</label>
                                            <input type="date" name="tanggal_pengumuman" id="editTglPengumuman" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        </div>
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

    <!-- MODAL VIEW DETAIL PAKET -->
    <div x-show="viewModalOpen" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-view" role="dialog" aria-modal="true"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div @click="viewModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-view">Detail Paket Pelatihan</h3>
                            <div class="mt-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Jenis Pelatihan</label>
                                        <p class="mt-1 text-sm text-gray-900 font-medium" x-text="viewData.jenis"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Tahun</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tahun"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Batch</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.batch || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">JP Harian</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.jp_harian || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Sabtu Masuk</label>
                                        <p class="mt-1">
                                            <span 
                                                :class="viewData.sabtu_masuk === 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                class="px-2.5 py-0.5 text-xs font-medium rounded-full" 
                                                x-text="viewData.sabtu_masuk === 'Y' ? 'Ya' : 'Tidak'">
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Minggu Masuk</label>
                                        <p class="mt-1">
                                            <span 
                                                :class="viewData.minggu_masuk === 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                                class="px-2.5 py-0.5 text-xs font-medium rounded-full" 
                                                x-text="viewData.minggu_masuk === 'Y' ? 'Ya' : 'Tidak'">
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Tanggal Mulai</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_mulai || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Tanggal Akhir</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_akhir || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Awal Pendaftaran</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_awal_pendaftaran || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Akhir Pendaftaran</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_akhir_pendaftaran || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Awal Tes Tulis</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_awal_tes_tulis || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Akhir Tes Tulis</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_akhir_tes_tulis || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Awal Wawancara</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_awal_wawancara || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Akhir Wawancara</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_akhir_wawancara || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Awal Daftar Ulang</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_awal_daftar_ulang || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Akhir Daftar Ulang</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_akhir_daftar_ulang || '-'"></p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Tanggal Pengumuman</label>
                                        <p class="mt-1 text-sm text-gray-900" x-text="viewData.tgl_pengumuman || '-'"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end">
                                <button @click="viewModalOpen = false" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
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
    <!-- MODAL PIVOT MANAGEMENT - COMPLETE FIXED VERSION -->
<div x-show="pivotModalOpen" 
     style="display: none" 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
        <!-- Backdrop -->
        <div @click="pivotModalOpen = false" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <!-- Modal Container -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full max-h-[90vh] overflow-y-auto"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Kelola Data Paket Pelatihan</h3>
                            <button @click="pivotModalOpen = false" class="text-gray-400 hover:text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- TAB NAVIGATION -->
                        <div class="border-b border-gray-200">
                            <div class="flex space-x-8 overflow-x-auto">
                                <button 
                                    @click="pivotTab = 'units'" 
                                    :class="{'border-b-2 border-blue-500 text-blue-600': pivotTab === 'units', 'text-gray-500 hover:text-gray-700': pivotTab !== 'units'}" 
                                    class="py-2 px-1 text-sm font-medium transition whitespace-nowrap">
                                    Paket Units
                                </button>
                                <button 
                                    @click="pivotTab = 'sub-units'" 
                                    :class="{'border-b-2 border-blue-500 text-blue-600': pivotTab === 'sub-units', 'text-gray-500 hover:text-gray-700': pivotTab !== 'sub-units'}" 
                                    class="py-2 px-1 text-sm font-medium transition whitespace-nowrap">
                                    Paket Sub Units
                                </button>
                            </div>
                        </div>

                        <!-- TAB CONTENT -->
                        <div class="mt-6">
                            <div x-show="pivotTab === 'units'" class="space-y-6">
    <!-- FORM TAMBAH DENGAN DROPDOWN PROGRAM YANG AMBIL DATA -->
    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
        <h4 class="text-base font-medium mb-4">Tambah Unit ke Paket</h4>

        <form method="POST" 
              x-bind:action="`/admin/programs/paket-pelatihan/${currentPaketId}/paket-units`"
              @submit="if(!currentPaketId) { alert('Paket ID tidak ditemukan'); return false; }">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- DROPDOWN PILIH PROGRAM -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pilih Program *
                    </label>
                    <select 
                        name="program_id" 
                        id="programSelectDropdown"
                        required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih Program untuk Paket Ini --</option>
                    </select>
                    
                    <p id="noProgramMessage" class="mt-1 text-xs text-red-600 hidden">
                        Paket ini belum memiliki program. Silakan tambahkan program ke paket terlebih dahulu di menu 
                        <a href="{{ route('admin.programs.index') }}" class="underline font-medium">Kelola Pelatihan</a>.
                    </p>

                    <!-- DATA PROGRAMS DALAM FORMAT JSON -->
                    <script>
                        window.paketProgramsData = {!! json_encode($pakets->map(function($paket) {
                            return [
                                'paket_id' => $paket->id,
                                'programs' => $paket->programs->map(function($program) {
                                    return [
                                        'id' => $program->id,
                                        'name' => ($program->masterProgram->name ?? 'Program #' . $program->id) . 
                                                 ($program->angkatan ? ' - Angkatan ' . $program->angkatan : '')
                                    ];
                                })->toArray()
                            ];
                        })->keyBy('paket_id')) !!};

                        // Function untuk update dropdown berdasarkan paket yang dipilih
                        function updateProgramDropdown(paketId) {
                            const dropdown = document.getElementById('programSelectDropdown');
                            const noProgMessage = document.getElementById('noProgramMessage');
                            
                            // Clear existing options
                            dropdown.innerHTML = '<option value="">-- Pilih Program untuk Paket Ini --</option>';
                            
                            // Cari data programs untuk paket ini
                            const paketData = window.paketProgramsData[paketId];
                            
                            if (paketData && paketData.programs && paketData.programs.length > 0) {
                                // Ada programs, tambahkan ke dropdown
                                paketData.programs.forEach(program => {
                                    const option = document.createElement('option');
                                    option.value = program.id;
                                    option.textContent = program.name;
                                    dropdown.appendChild(option);
                                });
                                noProgMessage.classList.add('hidden');
                            } else {
                                // Tidak ada programs
                                noProgMessage.classList.remove('hidden');
                            }
                        }

                        // Watch untuk perubahan currentPaketId
                        document.addEventListener('alpine:initialized', () => {
                            Alpine.effect(() => {
                                const paketId = Alpine.store('paketData') ? Alpine.store('paketData').currentPaketId : null;
                                if (paketId) {
                                    updateProgramDropdown(paketId);
                                }
                            });
                        });
                    </script>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Program Unit *</label>
                    <select name="program_pelatihan_unit_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Cari Unit Kompetensi --</option>
                        @foreach($programPelatihanUnits as $unit)
                            <option value="{{ $unit->id }}">
                                {{ $unit->independentCompetencyUnit->name ?? $unit->independentCompetencyUnit->code ?? 'Unit #' . $unit->id }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Master Sub Unit *</label>
                    <select name="master_program_sub_unit_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Pilih --</option>
                        @foreach($masterPrograms as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">JP (Jam Pelajaran)</label>
                    <input type="number" name="jp" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: 120">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sub Unit Kompetensi? *</label>
                    <select name="sub_unit_kompetensi" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="N">Tidak</option>
                        <option value="Y">Ya</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Tambah Unit ke Paket
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DAFTAR UNIT -->
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Master Sub Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">JP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub Unit Kom</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($pakets as $paket)
                    @php
                        $allUnits = collect();
                        foreach($paket->programs as $prog) {
                            $units = \App\Models\PaketPelatihanUnit::where('programs_id', $prog->id)
                                ->with(['programPelatihanUnit.independentCompetencyUnit', 'masterProgramSubUnit', 'program.masterProgram'])
                                ->get();
                            $allUnits = $allUnits->merge($units);
                        }
                    @endphp
                    
                    <template x-if="currentPaketId === '{{ $paket->id }}'">
                        @forelse($allUnits as $unit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">
                                    <span class="font-medium text-indigo-600">
                                        {{ $unit->program->masterProgram->name ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $unit->programPelatihanUnit->independentCompetencyUnit->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $unit->masterProgramSubUnit->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $unit->jp ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $unit->sub_unit_kompetensi == 'Y' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $unit->sub_unit_kompetensi == 'Y' ? 'Ya' : 'Tidak' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="/admin/programs/paket-pelatihan/{{ $paket->id }}/paket-units/{{ $unit->id }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus?')" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500 text-sm">
                                    Belum ada unit di paket ini
                                </td>
                            </tr>
                        @endforelse
                    </template>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

                            <!-- ============================================ -->
                            <!-- TAB SUB-UNITS -->
                            <!-- ============================================ -->
                            <div x-show="pivotTab === 'sub-units'" class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <p class="text-sm text-gray-600 mb-3">Tambah Sub Unit Kompetensi ke Paket Unit</p>
                                    
                                    <form method="POST" :action="`/admin/programs/paket-pelatihan/${currentPaketId}/paket-sub-units`">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                                    Pilih Paket Unit <span class="text-red-500">*</span>
                                                </label>
                                                <select name="paket_pelatihan_unit_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                    <option value="">-- Pilih Unit --</option>
                                                    @foreach($pakets as $paket)
                                                        @php
                                                            $currentProgram = $paket->programs->first();
                                                            $availableUnits = $currentProgram 
                                                                ? \App\Models\PaketPelatihanUnit::where('programs_id', $currentProgram->id)
                                                                    ->with('programPelatihanUnit.independentCompetencyUnit')
                                                                    ->get() 
                                                                : collect([]);
                                                        @endphp
                                                        <template x-if="currentPaketId === '{{ $paket->id }}'">
                                                            @foreach($availableUnits as $pu)
                                                                <option value="{{ $pu->id }}">
                                                                    {{ $pu->programPelatihanUnit->independentCompetencyUnit->name ?? 'Unit #' . $pu->id }}
                                                                </option>
                                                            @endforeach
                                                        </template>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                                    Master Program <span class="text-red-500">*</span>
                                                </label>
                                                <select name="master_programs_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                    <option value="">-- Pilih Master Program --</option>
                                                    @foreach($masterPrograms as $mp)
                                                        <option value="{{ $mp->id }}">{{ $mp->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">
                                                    Unit Kompetensi Independen <span class="text-red-500">*</span>
                                                </label>
                                                <select name="independent_competency_units_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                                    <option value="">-- Pilih Unit Kompetensi --</option>
                                                    @foreach($allCompetencyUnits as $unit)
                                                        <option value="{{ $unit->id }}">{{ $unit->name ?? $unit->code }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">JP (Jam Pelajaran)</label>
                                                <input type="number" name="jp" min="0" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 80">
                                            </div>
                                        </div>

                                        <button type="submit" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                            Tambah Sub-Unit
                                        </button>
                                    </form>
                                </div>

                                <!-- Tabel Sub Units -->
                                <div class="border rounded-lg overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100 border-b">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Paket Unit</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Master Program</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Unit Kompetensi</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">JP</th>
                                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 w-20">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y">
                                            @foreach($pakets as $paket)
                                                @php
                                                    $currentProgram = $paket->programs->first();
                                                    $subUnits = $currentProgram 
                                                        ? \App\Models\PaketPelatihanSubUnit::whereHas('paketPelatihanUnit', function($q) use ($currentProgram) {
                                                            $q->where('programs_id', $currentProgram->id);
                                                        })->with(['paketPelatihanUnit.programPelatihanUnit.independentCompetencyUnit', 'masterProgram', 'unitKompetensi'])->get() 
                                                        : collect([]);
                                                @endphp
                                                
                                                <template x-if="currentPaketId === '{{ $paket->id }}'">
                                                    @forelse($subUnits as $subUnit)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-2 text-sm">
                                                            {{ $subUnit->paketPelatihanUnit->programPelatihanUnit->independentCompetencyUnit->name ?? '-' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm">{{ $subUnit->masterProgram->name ?? '-' }}</td>
                                                        <td class="px-4 py-2 text-sm">{{ $subUnit->unitKompetensi->name ?? $subUnit->unitKompetensi->code ?? '-' }}</td>
                                                        <td class="px-4 py-2 text-sm">{{ $subUnit->jp ?? '-' }}</td>
                                                        <td class="px-4 py-2 text-center">
                                                            <form action="/admin/programs/paket-pelatihan/{{ $paket->id }}/paket-sub-units/{{ $subUnit->id }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub unit ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">
                                                            Tidak ada sub unit. Tambahkan menggunakan form di atas.
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </template>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <!-- Footer Buttons -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <button @click="pivotModalOpen = false" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                            {{-- <!-- PESERTA TAB -->
                            <div x-show="pivotTab === 'peserta'" class="space-y-4">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-sm text-blue-800">
                                    ℹ️ Fitur Peserta akan ditambahkan setelah controller selesai
                                </div>
                            </div>

                            <!-- PENGAJAR TAB -->
                            <div x-show="pivotTab === 'pengajar'" class="space-y-4">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-sm text-blue-800">
                                    ℹ️ Fitur Pengajar akan ditambahkan setelah controller selesai
                                </div>
                            </div>

                            <!-- KOMPETENSI TAB -->
                            <div x-show="pivotTab === 'kompetensi'" class="space-y-4">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 text-sm text-blue-800">
                                    ℹ️ Fitur Kompetensi akan ditambahkan setelah controller selesai
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button @click="pivotModalOpen = false" class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">Tutup</button>
                        </div> --}}
                    {{-- </div>
                </div>
            </div>
        </div>
    </div>

</div> --}}
@endsection
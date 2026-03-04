@extends('layouts.app')

@section('title', 'Kelola Jenis Pelatihan')

@section('content')
<div
    class="space-y-6"
    x-data="{
        addModalOpen: false,
        editModalOpen: false,
        deleteModalOpen: false,

        editId: null,
        editNama: '',
        deleteId: null,

        openEdit(id, nama) {
            this.editId = id;
            this.editNama = nama;
            this.editModalOpen = true;
            this.$nextTick(() => {
                document.getElementById('editJenisForm').action = '{{ url('admin/programs/jenis-pelatihan') }}/' + id;
            });
        },

        openDelete(id) {
            this.deleteId = id;
            this.deleteModalOpen = true;
            this.$nextTick(() => {
                document.getElementById('deleteJenisForm').action = '{{ url('admin/programs/jenis-pelatihan') }}/' + id;
            });
        }
    }"
>

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Jenis Pelatihan</h2>
        <button
            @click="addModalOpen = true"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2 shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Jenis</span>
        </button>
    </div>

    {{-- <!-- Flash message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center space-x-3">
        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif --}}

    <!-- Tabel -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="w-full min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pelatihan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dibuat Oleh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diupdate</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            <!-- Tombol Edit -->
                            <button
                                type="button"
                                @click="openEdit('{{ $j->id }}', '{{ addslashes($j->jenis_pelatihan) }}')"
                                class="inline-flex items-center text-blue-600 hover:text-blue-900 transition"
                                title="Edit"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>

                            <!-- Tombol Hapus -->
                            <button
                                type="button"
                                @click="openDelete('{{ $j->id }}')"
                                class="inline-flex items-center text-red-600 hover:text-red-900 transition"
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

    <!-- ======================== -->
    <!-- Modal Tambah -->
    <!-- ======================== -->
    <div
        x-show="addModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none"
    >
        <div @click="addModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md z-10">
            <div class="px-6 py-5 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Jenis Pelatihan</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.programs.jenis-pelatihan.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_pelatihan" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="" disabled selected>-- Pilih Jenis --</option>
                        <option value="Non Boarding">Non Boarding</option>
                        <option value="Boarding">Boarding</option>
                        <option value="Project Based Learning (PBL)">Project Based Learning (PBL)</option>
                        <option value="Project Based Kompetensi (PBK)">Project Based Kompetensi (PBK)</option>
                        <option value="Tailor Made Training">Tailor Made Training</option>
                        <option value="PFLK">PFLK</option>
                    </select>
                    @error('jenis_pelatihan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t rounded-b-xl flex justify-end space-x-3">
                    <button type="button" @click="addModalOpen = false"
                        class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================== -->
    <!-- Modal Edit -->
    <!-- ======================== -->
    <div
        x-show="editModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none"
    >
        <div @click="editModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md z-10">
            <div class="px-6 py-5 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Edit Jenis Pelatihan</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            {{-- action di-set dinamis via openEdit() dengan $nextTick --}}
            <form id="editJenisForm" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_pelatihan" required x-model="editNama"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="" disabled>-- Pilih Jenis --</option>
                        <option value="Non Boarding">Non Boarding</option>
                        <option value="Boarding">Boarding</option>
                        <option value="Project Based Learning (PBL)">Project Based Learning (PBL)</option>
                        <option value="Project Based Kompetensi (PBK)">Project Based Kompetensi (PBK)</option>
                        <option value="Tailor Made Training">Tailor Made Training</option>
                        <option value="PFLK">PFLK</option>
                    </select>
                    @error('jenis_pelatihan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t rounded-b-xl flex justify-end space-x-3">
                    <button type="button" @click="editModalOpen = false"
                        class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================== -->
    <!-- Modal Hapus -->
    <!-- ======================== -->
    <div
        x-show="deleteModalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none"
    >
        <div @click="deleteModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md z-10">
            <div class="px-6 py-5">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Hapus Jenis Pelatihan</h3>
                        <p class="text-sm text-gray-500 mt-1">Apakah kamu yakin ingin menghapus jenis pelatihan ini? Aksi ini tidak bisa dibatalkan.</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t rounded-b-xl flex justify-end space-x-3">
                <button type="button" @click="deleteModalOpen = false"
                    class="px-4 py-2 text-sm bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                {{-- action di-set dinamis via openDelete() dengan $nextTick --}}
                <form id="deleteJenisForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
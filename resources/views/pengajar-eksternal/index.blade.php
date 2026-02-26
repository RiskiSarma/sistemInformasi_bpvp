@extends('layouts.app')

@section('title', 'Pengajar Eksternal')

@section('content')
<div class="space-y-6" x-data="{ 
    createModal: false,
    editModal: false,
    showModal: false,
    deleteModal: false,
    formData: {},
    showData: {},
    pengajarTipe: 'eksternal',
    errors: {},
    validateCreate() {
        this.errors = {};
        let isValid = true;
        
        const namaField = document.querySelector('[name=\'nama\']');
        const nikField = document.querySelector('[name=\'nik\']');
        const nipField = document.querySelector('[name=\'nip\']');
        const instansiField = document.querySelector('[name=\'instansi\']');
        const teleponField = document.querySelector('[name=\'telepon\']');
        const emailField = document.querySelector('[name=\'email\']');
        
        if (!namaField.value.trim()) {
            this.errors.nama = 'Nama lengkap wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) namaField.focus();
            return false;
        }
        
        if (!nikField.value.trim()) {
            this.errors.nik = 'NIK wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nikField.focus();
            return false;
        }
        
        if (nikField.value.trim().length < 16) {
            this.errors.nik = 'NIK harus 16 digit';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nikField.focus();
            return false;
        }
        
        if (!nipField.value.trim()) {
            this.errors.nip = 'NIP wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nipField.focus();
            return false;
        }
        
        if (!instansiField.value.trim()) {
            this.errors.instansi = 'Nama instansi wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) instansiField.focus();
            return false;
        }
        
        if (!teleponField.value.trim()) {
            this.errors.telepon = 'Nomor telepon wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) teleponField.focus();
            return false;
        }
        
        if (!emailField.value.trim()) {
            this.errors.email = 'Alamat email wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) emailField.focus();
            return false;
        }
        
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailField.value)) {
            this.errors.email = 'Format email tidak valid';
            isValid = false;
            if (Object.keys(this.errors).length === 1) emailField.focus();
            return false;
        }
        
        return isValid;
    },
    validateEdit() {
        this.errors = {};
        let isValid = true;
        
        const form = document.querySelector('#editForm');
        const namaField = form.querySelector('[name=\'nama\']');
        const nikField = form.querySelector('[name=\'nik\']');
        const nipField = form.querySelector('[name=\'nip\']');
        const instansiField = form.querySelector('[name=\'instansi\']');
        const teleponField = form.querySelector('[name=\'telepon\']');
        const emailField = form.querySelector('[name=\'email\']');
        
        if (!namaField.value.trim()) {
            this.errors.nama = 'Nama lengkap wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) namaField.focus();
            return false;
        }
        
        if (!nikField.value.trim()) {
            this.errors.nik = 'NIK wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nikField.focus();
            return false;
        }
        
        if (nikField.value.trim().length < 16) {
            this.errors.nik = 'NIK harus 16 digit';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nikField.focus();
            return false;
        }
        
        if (!nipField.value.trim()) {
            this.errors.nip = 'NIP wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) nipField.focus();
            return false;
        }
        
        if (!instansiField.value.trim()) {
            this.errors.instansi = 'Nama instansi wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) instansiField.focus();
            return false;
        }
        
        if (!teleponField.value.trim()) {
            this.errors.telepon = 'Nomor telepon wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) teleponField.focus();
            return false;
        }
        
        if (!emailField.value.trim()) {
            this.errors.email = 'Alamat email wajib diisi';
            isValid = false;
            if (Object.keys(this.errors).length === 1) emailField.focus();
            return false;
        }
        
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailField.value)) {
            this.errors.email = 'Format email tidak valid';
            isValid = false;
            if (Object.keys(this.errors).length === 1) emailField.focus();
            return false;
        }
        
        return isValid;
    }
}">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengajar Eksternal</h2>
            <p class="text-gray-600 mt-1">Kelola data pengajar dari luar institusi</p>
        </div>
        <button @click.stop="createModal = true; errors = {}" 
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center space-x-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Pengajar</span>
        </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pengajar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nama, NIK, email, instansi..."
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            @if(request('search'))
            <a href="{{ route('admin.pengajar-eksternal.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instansi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pengajars as $pengajar)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $pengajars->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $pengajar->nama }}</div>
                            <div class="text-xs text-gray-500">{{ $pengajar->jabatan ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pengajar->nik ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pengajar->nip ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $pengajar->instansi }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900">{{ $pengajar->telepon }}</div>
                            <div class="text-xs text-gray-500">{{ $pengajar->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- VIEW -->
                                <button @click="
    console.log('Mengambil detail untuk ID: {{ $pengajar->id }}');
    fetch('/admin/pengajar-eksternal/' + '{{ $pengajar->id }}' + '/detail')
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil data');
            return response.json();
        })
        .then(data => {
            showData = data;
            showModal = false;
            setTimeout(() => {
                showModal = true;
                activeTab = 'info';
            }, 50);
            console.log('Data berhasil diambil:', data);
        })
        .catch(error => {
            console.error('Error fetch detail:', error);
            alert('Gagal memuat detail pengajar: ' + error.message);
        });
                                " class="text-blue-600 hover:text-blue-800 transition" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                <!-- EDIT -->
                                <button @click="
                                    formData = {
                                        id: '{{ $pengajar->id }}',
                                        nama: '{{ addslashes($pengajar->nama) }}',
                                        nik: '{{ $pengajar->nik }}',
                                        nip: '{{ $pengajar->nip }}',
                                        instansi: '{{ addslashes($pengajar->instansi) }}',
                                        jabatan: '{{ addslashes($pengajar->jabatan) }}',
                                        alamat: `{{ addslashes($pengajar->alamat) }}`,
                                        telepon: '{{ $pengajar->telepon }}',
                                        email: '{{ $pengajar->email }}',
                                        pendidikan_id: '{{ $pengajar->pendidikan_id }}',
                                        kejuruan_pendidikan: '{{ addslashes($pengajar->kejuruan_pendidikan) }}'
                                    };
                                    errors = {};
                                    editModal = true;
                                " 
                                class="text-green-600 hover:text-green-800 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <!-- DELETE -->
                                <button @click="
                                    formData = { id: '{{ $pengajar->id }}', nama: '{{ addslashes($pengajar->nama) }}' };
                                    deleteModal = true;
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 font-medium">Belum ada data pengajar eksternal</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajars->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">{{ $pengajars->links() }}</div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- MODAL CREATE --}}
    {{-- ========================================= --}}
    <div x-show="createModal" 
         style="display: none" 
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="createModal = false; errors = {}" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            <div class="relative bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Tambah Pengajar Eksternal</h3>
                        <button @click="createModal = false; errors = {}" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form id="createForm"
                          method="POST" 
                          action="{{ route('admin.pengajar-eksternal.store') }}" 
                          class="space-y-4"
                          @submit.prevent="if (validateCreate()) $el.submit()">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Lengkap -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.nama}"
                                       value="{{ old('nama') }}">
                                @error('nama')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.nama" class="mt-1 text-sm text-red-600" x-text="errors.nama"></p>
                                @enderror
                            </div>

                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NIK <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nik" 
                                       maxlength="16" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nik') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.nik}"
                                       value="{{ old('nik') }}"
                                       @input="$el.value = $el.value.replace(/\D/g, '')">
                                @error('nik')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.nik" class="mt-1 text-sm text-red-600" x-text="errors.nik"></p>
                                @enderror
                            </div>

                            <!-- NIP -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NIP <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nip" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nip') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.nip}"
                                       value="{{ old('nip') }}">
                                @error('nip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.nip" class="mt-1 text-sm text-red-600" x-text="errors.nip"></p>
                                @enderror
                            </div>

                            <!-- Instansi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Instansi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="instansi" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('instansi') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.instansi}"
                                       value="{{ old('instansi') }}">
                                @error('instansi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.instansi" class="mt-1 text-sm text-red-600" x-text="errors.instansi"></p>
                                @enderror
                            </div>

                            <!-- Jabatan -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <input type="text" 
                                       name="jabatan" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('jabatan') }}">
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="alamat" 
                                          rows="2" 
                                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('alamat') }}</textarea>
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="telepon" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('telepon') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.telepon}"
                                       value="{{ old('telepon') }}"
                                       @input="$el.value = $el.value.replace(/[^0-9+\-\s]/g, '')">
                                @error('telepon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.telepon" class="mt-1 text-sm text-red-600" x-text="errors.telepon"></p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                                       :class="{'border-red-500': errors.email}"
                                       value="{{ old('email') }}">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @else
                                    <p x-show="errors.email" class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
                                @enderror
                            </div>

                            <!-- Pendidikan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_id"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach($pendidikans as $pend)
                                        <option value="{{ $pend->id }}" {{ old('pendidikan_id') == $pend->id ? 'selected' : '' }}>
                                            {{ $pend->pendidikan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kejuruan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kejuruan</label>
                                <input type="text" 
                                       name="kejuruan_pendidikan" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" 
                                       value="{{ old('kejuruan_pendidikan') }}">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
                            <button type="button" 
                                    @click="createModal = false; errors = {}" 
                                    class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- MODAL EDIT --}}
    {{-- ========================================= --}}
    <div x-show="editModal" style="display: none" class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="editModal = false; errors = {}" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Edit Pengajar Eksternal</h3>
                        <button @click="editModal = false; errors = {}" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form id="editForm"
                          :action="'{{ route('admin.pengajar-eksternal.index') }}/' + formData.id" 
                          method="POST" 
                          class="space-y-4"
                          @submit.prevent="if (validateEdit()) $el.submit()">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nama Lengkap -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nama" 
                                       :value="formData.nama" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.nama}">
                                <p x-show="errors.nama" class="mt-1 text-sm text-red-600" x-text="errors.nama"></p>
                            </div>

                            <!-- NIK -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NIK <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nik" 
                                       :value="formData.nik" 
                                       maxlength="16"
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.nik}"
                                       @input="$el.value = $el.value.replace(/\D/g, '')">
                                <p x-show="errors.nik" class="mt-1 text-sm text-red-600" x-text="errors.nik"></p>
                            </div>

                            <!-- NIP -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    NIP <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="nip" 
                                       :value="formData.nip" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.nip}">
                                <p x-show="errors.nip" class="mt-1 text-sm text-red-600" x-text="errors.nip"></p>
                            </div>

                            <!-- Instansi -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Instansi <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="instansi" 
                                       :value="formData.instansi" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.instansi}">
                                <p x-show="errors.instansi" class="mt-1 text-sm text-red-600" x-text="errors.instansi"></p>
                            </div>

                            <!-- Jabatan -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <input type="text" 
                                       name="jabatan" 
                                       :value="formData.jabatan" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                <textarea name="alamat" 
                                          rows="2" 
                                          class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" 
                                          x-text="formData.alamat"></textarea>
                            </div>

                            <!-- Telepon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="telepon" 
                                       :value="formData.telepon" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.telepon}"
                                       @input="$el.value = $el.value.replace(/[^0-9+\-\s]/g, '')">
                                <p x-show="errors.telepon" class="mt-1 text-sm text-red-600" x-text="errors.telepon"></p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       :value="formData.email" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                                       :class="{'border-red-500': errors.email}">
                                <p x-show="errors.email" class="mt-1 text-sm text-red-600" x-text="errors.email"></p>
                            </div>

                            <!-- Pendidikan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                                <select name="pendidikan_id"
                                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach($pendidikans as $pend)
                                        <option value="{{ $pend->id }}"
                                                :selected="formData.pendidikan_id == {{ $pend->id }}">
                                            {{ $pend->pendidikan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Kejuruan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kejuruan</label>
                                <input type="text" 
                                       name="kejuruan_pendidikan" 
                                       :value="formData.kejuruan_pendidikan" 
                                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4 border-t mt-6">
                            <button type="button" 
                                    @click="editModal = false; errors = {}" 
                                    class="px-6 py-2 border rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
{{-- MODAL SHOW/VIEW LENGKAP - MIRIP MASTER-SHOW --}}
{{-- Ganti modal show yang ada dengan code ini --}}
{{-- ========================================= --}}
<div x-show="showModal" 
     style="display: none" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     x-transition
     x-data="{ 
         activeTab: 'info',
         assignProgramModal: false,
         assignSubUnitModal: false,
         editProgramModal: false,
         editSubUnitModal: false,
         deleteModal: false,
         deleteType: '',
         editFormData: {},
         deleteData: {},
         createModal: false
     }">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div @click="showModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 z-10">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Detail Pengajar Eksternal</h3>
                    <button @click="showModal = false; activeTab = 'info'" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Tab Navigation -->
                <div class="flex space-x-6 mt-4 border-t pt-2">
                    <button @click="activeTab = 'info'"
                            :class="activeTab === 'info' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="py-2 font-medium text-sm transition">
                        Info Dasar
                    </button>
                    <button @click="activeTab = 'programs'"
                            :class="activeTab === 'programs' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="py-2 font-medium text-sm transition">
                        Program (Jenis Materi)
                    </button>
                    <button @click="activeTab = 'subunits'"
                            :class="activeTab === 'subunits' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                            class="py-2 font-medium text-sm transition">
                        Sub Units
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
                
                <!-- =============================================== -->
                <!-- TAB 1: INFO DASAR -->
                <!-- =============================================== -->
                <div x-show="activeTab === 'info'" x-transition>
                    <!-- Profile Header -->
                    <div class="flex items-start space-x-4 mb-6 pb-6 border-b">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <span x-text="showData.nama ? showData.nama.charAt(0).toUpperCase() : 'P'"></span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900" x-text="showData.nama"></h4>
                            <p class="text-gray-600" x-text="showData.jabatan"></p>
                            <p class="text-gray-600 font-medium" x-text="showData.instansi"></p>
                            <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span x-text="showData.telepon"></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="showData.email"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Sections -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Data Pribadi -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <h5 class="font-semibold text-gray-900">Data Pribadi</h5>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">NIK</p>
                                    <p class="font-medium text-gray-900" x-text="showData.nik"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">NIP</p>
                                    <p class="font-medium text-gray-900" x-text="showData.nip"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Data Institusi -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <h5 class="font-semibold text-gray-900">Data Institusi</h5>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Instansi</p>
                                    <p class="font-medium text-gray-900" x-text="showData.instansi"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Jabatan</p>
                                    <p class="font-medium text-gray-900" x-text="showData.jabatan"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Alamat</p>
                                    <p class="font-medium text-gray-900 text-sm" x-text="showData.alamat"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Pendidikan -->
                        <!-- Di dalam div x-show="activeTab === 'info'" -->
                        <div class="bg-gray-50 rounded-lg p-5">
                            <div class="flex items-center mb-4">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                                </svg>
                                <h5 class="font-semibold text-gray-900">Pendidikan</h5>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Jenjang Pendidikan</p>
                                    <p class="font-medium text-gray-900" x-text="showData.pendidikan || 'Belum diisi'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Kejuruan / Bidang Studi</p>
                                    <p class="font-medium text-gray-900" x-text="showData.kejuruan || '-'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- =============================================== -->
                <!-- TAB 2: PROGRAM (JENIS MATERI) -->
                <!-- =============================================== -->
                <div x-show="activeTab === 'programs'" x-transition>
                    <div class="space-y-4">
                        <!-- Header -->
                        <div class="flex justify-between items-center pb-4 border-b">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajar</h3>
                                <p class="text-sm text-gray-600 mt-1">Total: <span x-text="showData.programAssignments ? showData.programAssignments.length : 0"></span> pengajar program</p>
                            </div>
                            <button @click.stop="assignProgramModal = true"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>+ Assign Pengajar</span>
                            </button>
                        </div>

                        <!-- Table Assignments -->
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NO</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NAMA</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TIPE</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PROGRAM</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">JENIS MATERI</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">INSTANSI</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <template x-if="showData.programAssignments && showData.programAssignments.length > 0">
                                        <template x-for="(assignment, index) in showData.programAssignments" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                <td x-text="assignment.nama_pengajar"></td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full"
                                                          :class="assignment.tipe === 'Eksternal' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                          x-text="assignment.tipe"></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-sm text-gray-900" x-text="assignment.program_name"></div>
                                                    <div class="text-xs text-gray-500" x-text="assignment.angkatan ? 'Angkatan ' + assignment.angkatan : '-'"></div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800" x-text="assignment.jenis_materi"></span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900" x-text="assignment.instansi || '-'"></td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <!-- Edit Button -->
                                                        <button @click="
                                                            editFormData = {
                                                                id: assignment.id,
                                                                programs_id: assignment.programs_id,
                                                                jenis_materi_pelatihan_id: assignment.jenis_materi_id,
                                                                pengajar_tipe: assignment.tipe === 'Eksternal' ? 'eksternal' : 'internal',
                                                                pengajar_internal_id: assignment.pengajar_internal_id,
                                                                pengajar_eksternal_id: assignment.pengajar_eksternal_id
                                                            };
                                                            editProgramModal = true;
                                                        " class="text-green-600 hover:text-green-800" title="Edit">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <button @click="
                                                            deleteData = { id: assignment.id, name: assignment.program_name };
                                                            deleteType = 'program';
                                                            deleteModal = true;
                                                        " class="text-red-600 hover:text-red-800" title="Hapus">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                    
                                    <!-- Empty State -->
                                    <tr x-show="!showData.programAssignments || showData.programAssignments.length === 0">
                                        <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="font-medium">Belum ada pengajar yang di-assign</p>
                                            <p class="text-sm mt-1">Klik tombol "Assign Pengajar" untuk menambahkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- =============================================== -->
                <!-- TAB 3: SUB UNITS -->
                <!-- =============================================== -->
                <div x-show="activeTab === 'subunits'" x-transition>
                    <div class="space-y-4">
                        <!-- Header -->
                        <div class="flex justify-between items-center pb-4 border-b">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Daftar Pengajar Sub Unit</h3>
                                <p class="text-sm text-gray-600 mt-1">Total: <span x-text="showData.subUnitAssignments ? showData.subUnitAssignments.length : 0"></span> pengajar sub unit</p>
                            </div>
                            <button @click="assignSubUnitModal = true"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>+ Assign ke Sub Unit</span>
                            </button>
                        </div>

                        <!-- Table Sub Unit Assignments -->
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">NO</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PAKET UNIT</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PROGRAM</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TIPE</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    <template x-if="showData.subUnitAssignments && showData.subUnitAssignments.length > 0">
                                        <template x-for="(subAssignment, index) in showData.subUnitAssignments" :key="index">
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm" x-text="index + 1"></td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-sm text-gray-900" x-text="subAssignment.paket_unit_name"></div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900" x-text="subAssignment.program_name"></td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full"
                                                          :class="subAssignment.tipe === 'Eksternal' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                                          x-text="subAssignment.tipe"></span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <!-- Edit Button -->
                                                        <button @click="
                                                            editFormData = {
                                                                id: subAssignment.id,
                                                                paket_pelatihan_unit_id: subAssignment.paket_pelatihan_unit_id,
                                                                programs_id: subAssignment.programs_id,
                                                                pengajar_tipe: subAssignment.tipe === 'Eksternal' ? 'eksternal' : 'internal',
                                                                pengajar_internal_id: subAssignment.pengajar_internal_id,
                                                                pengajar_eksternal_id: subAssignment.pengajar_eksternal_id
                                                            };
                                                            editSubUnitModal = true;
                                                        " class="text-green-600 hover:text-green-800" title="Edit">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <button @click="
                                                            deleteData = { id: subAssignment.id, name: subAssignment.paket_unit_name };
                                                            deleteType = 'subunit';
                                                            deleteModal = true;
                                                        " class="text-red-600 hover:text-red-800" title="Hapus">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                    
                                    <!-- Empty State -->
                                    <tr x-show="!showData.subUnitAssignments || showData.subUnitAssignments.length === 0">
                                        <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="font-medium">Belum ada pengajar sub unit yang di-assign</p>
                                            <p class="text-sm mt-1">Klik tombol "Assign ke Sub Unit" untuk menambahkan</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 bg-white border-t px-6 py-4">
                <div class="flex justify-end">
                    <button @click="showModal = false; activeTab = 'info'" 
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- NESTED MODAL: ASSIGN TO PROGRAM -->
    <!-- ========================================= -->
    <!-- NESTED MODAL: ASSIGN TO PROGRAM -->
<div x-show="assignProgramModal"
     style="display: none"
     class="fixed inset-0 z-[60] overflow-y-auto"
     x-transition
     x-data="{ pengajarTipe: 'internal',localErrors: {} }">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div @click="assignProgramModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-xl w-full">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between rounded-t-xl">
                <h3 class="text-xl font-bold text-gray-900">Assign Pengajar ke Program</h3>
                <button @click="assignProgramModal = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form method="POST" :action="'/admin/pengajar-eksternal/' + showData.id + '/assign-program'"
                      x-data="{ pengajarTipe: 'internal' }">
                    @csrf
                    <div class="space-y-5">
                        <!-- Jenis Materi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Materi Pelatihan <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_materi_pelatihan_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Jenis Materi --</option>
                                @foreach(\App\Models\JenisMateriPelatihan::orderBy('jenis_materi_pelatihan')->get() as $jm)
                                    <option value="{{ $jm->id }}">{{ $jm->jenis_materi_pelatihan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipe Pengajar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Pengajar <span class="text-red-500">*</span>
                            </label>
                            <div class="flex space-x-8">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe" required
                                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Internal</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="pengajar_tipe" value="eksternal" x-model="pengajarTipe"
                                           class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Eksternal</span>
                                </label>
                            </div>
                        </div>

                        <!-- Pengajar Internal -->
                        <div x-show="pengajarTipe === 'internal'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Instruktur <span class="text-red-500">*</span>
                            </label>
                            <select name="pengajar_internal_id" :required="pengajarTipe === 'internal'"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach(\App\Models\Instructor::orderBy('name')->get() as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pengajar Eksternal -->
                        <div x-show="pengajarTipe === 'eksternal'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Pengajar Eksternal <span class="text-red-500">*</span>
                            </label>
                            <select name="pengajar_eksternal_id" :required="pengajarTipe === 'eksternal'"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Pengajar --</option>
                                @foreach(\App\Models\PengajarEksternal::orderBy('nama')->get() as $pe)
                                    <option value="{{ $pe->id }}">{{ $pe->nama }} ({{ $pe->instansi }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Program -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pilih Program <span class="text-red-500">*</span>
                            </label>
                            <select name="programs_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Program --</option>
                                @foreach(\App\Models\Program::with('masterProgram')->orderBy('created_at', 'desc')->get() as $prog)
                                    <option value="{{ $prog->id }}">
                                        {{ $prog->masterProgram->name ?? 'Program' }}
                                        @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                        <button type="button" @click="assignProgramModal = false"
                                class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                @click.prevent="$nextTick(() => $el.form.submit())"
                                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Assign Pengajar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- NESTED MODAL: EDIT PROGRAM ASSIGNMENT -->
    <div x-show="editProgramModal"
        style="display: none"
        class="fixed inset-0 z-[60] overflow-y-auto"
        x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="editProgramModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-xl w-full">
                <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h3 class="text-xl font-bold text-gray-900">Edit Assignment Pengajar</h3>
                    <button @click="editProgramModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <form method="POST"
                        :action="`/admin/pengajar-programs/${editFormData.id}`"
                        x-data="{ pengajarTipe: editFormData.pengajar_tipe || 'internal' }">
                        @csrf
                        @method('PUT')
                        <div class="space-y-5">
                            <!-- Jenis Materi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jenis Materi Pelatihan <span class="text-red-500">*</span>
                                </label>
                                <select name="jenis_materi_pelatihan_id" required
                                        x-model="editFormData.jenis_materi_pelatihan_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Jenis Materi --</option>
                                    @foreach(\App\Models\JenisMateriPelatihan::orderBy('jenis_materi_pelatihan')->get() as $jm)
                                        <option value="{{ $jm->id }}">{{ $jm->jenis_materi_pelatihan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tipe Pengajar -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Pengajar <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-8">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe" required
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Internal</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="pengajar_tipe" value="eksternal" x-model="pengajarTipe"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Eksternal</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Pengajar Internal -->
                            <div x-show="pengajarTipe === 'internal'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Instruktur <span class="text-red-500">*</span>
                                </label>
                                <select name="pengajar_internal_id" :required="pengajarTipe === 'internal'"
                                        x-model="editFormData.pengajar_internal_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Instruktur --</option>
                                    @foreach(\App\Models\Instructor::orderBy('name')->get() as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pengajar Eksternal -->
                            <div x-show="pengajarTipe === 'eksternal'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Pengajar Eksternal <span class="text-red-500">*</span>
                                </label>
                                <select name="pengajar_eksternal_id" :required="pengajarTipe === 'eksternal'"
                                        x-model="editFormData.pengajar_eksternal_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Pengajar --</option>
                                    @foreach(\App\Models\PengajarEksternal::orderBy('nama')->get() as $pe)
                                        <option value="{{ $pe->id }}">{{ $pe->nama }} ({{ $pe->instansi }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Program -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Program <span class="text-red-500">*</span>
                                </label>
                                <select name="programs_id" required
                                        x-model="editFormData.programs_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Program --</option>
                                    @foreach(\App\Models\Program::with('masterProgram')->orderBy('created_at', 'desc')->get() as $prog)
                                        <option value="{{ $prog->id }}">
                                            {{ $prog->masterProgram->name ?? 'Program' }}
                                            @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" @click="editProgramModal = false"
                                    class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- NESTED MODAL: ASSIGN KE SUB UNIT - INI YANG DIPERBAIKI -->
        <div x-show="assignSubUnitModal"
             style="display: none"
             class="fixed inset-0 z-[60] overflow-y-auto"
             x-transition>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div @click="assignSubUnitModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
                <div class="relative bg-white rounded-xl shadow-2xl max-w-xl w-full">
                    <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-xl font-bold text-gray-900">Assign ke Sub Unit</h3>
                        <button @click="assignSubUnitModal = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <form method="POST" action="/admin/assign-pengajar-sub-unit">
                            @csrf
                            <div class="space-y-5">
                                <!-- Program -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Program <span class="text-red-500">*</span>
                                    </label>
                                    <select name="programs_id" required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih Program --</option>
                                        @foreach(\App\Models\Program::with('masterProgram')->orderBy('created_at', 'desc')->get() as $prog)
                                            <option value="{{ $prog->id }}">
                                                {{ $prog->masterProgram->name ?? 'Program' }}
                                                @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Paket Unit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Paket Unit <span class="text-red-500">*</span>
                                    </label>
                                    <select name="pp_unit_id" required
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih Paket Unit --</option>
                                        @foreach(\App\Models\PaketPelatihanUnit::with('programPelatihanUnit.independentCompetencyUnit')->get() as $pu)
                                            <option value="{{ $pu->id }}">
                                                {{ $pu->programPelatihanUnit->independentCompetencyUnit->name ?? 'Unit #' . $pu->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tipe Pengajar -->
                                <div x-data="{ pengajarTipe: 'internal' }">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipe Pengajar <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex space-x-8">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe" required
                                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Internal</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer">
                                            <input type="radio" name="pengajar_tipe" value="eksternal" x-model="pengajarTipe"
                                                   class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Eksternal</span>
                                        </label>
                                    </div>

                                    <div x-show="pengajarTipe === 'internal'" x-transition>
                                        <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">
                                            Pilih Instruktur <span class="text-red-500">*</span>
                                        </label>
                                        <select name="pengajar_internal_id" :required="pengajarTipe === 'internal'"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Instruktur --</option>
                                            @foreach(\App\Models\Instructor::orderBy('name')->get() as $instructor)
                                                <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div x-show="pengajarTipe === 'eksternal'" x-transition>
                                        <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">
                                            Pilih Pengajar Eksternal <span class="text-red-500">*</span>
                                        </label>
                                        <select name="pengajar_eksternal_id" :required="pengajarTipe === 'eksternal'"
                                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Pengajar --</option>
                                            @foreach(\App\Models\PengajarEksternal::orderBy('nama')->get() as $pe)
                                                <option value="{{ $pe->id }}">{{ $pe->nama }} ({{ $pe->instansi }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                                <button type="button" @click="assignSubUnitModal = false"
                                        class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Assign
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <!-- ========================================= -->
    <!-- NESTED MODAL: EDIT SUB UNIT ASSIGNMENT (FIXED) -->
    <!-- ========================================= -->
    <div x-show="editSubUnitModal"
        style="display: none"
        class="fixed inset-0 z-[60] overflow-y-auto"
        x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="editSubUnitModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-xl w-full">
                <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between rounded-t-xl">
                    <h3 class="text-xl font-bold text-gray-900">Edit Assignment Sub Unit</h3>
                    <button @click="editSubUnitModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <form method="POST"
                        :action="'/admin/pengajar-sub-units/' + editFormData.id"
                        x-data="{ pengajarTipe: editFormData.pengajar_tipe || 'internal' }">
                        @csrf
                        @method('PUT')
                        <div class="space-y-5">
                            <!-- Program -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Program <span class="text-red-500">*</span>
                                </label>
                                <select name="programs_id" required
                                        x-model="editFormData.programs_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Program --</option>
                                    @foreach(\App\Models\Program::with('masterProgram')->get() as $prog)
                                        <option value="{{ $prog->id }}">
                                            {{ $prog->masterProgram->name ?? 'Program' }}
                                            @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Paket Unit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Paket Unit <span class="text-red-500">*</span>
                                </label>
                                <select name="pp_unit_id" required
                                        x-model="editFormData.pp_unit_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Paket Unit --</option>
                                    @foreach(\App\Models\PaketPelatihanUnit::with('programPelatihanUnit.independentCompetencyUnit')->get() as $pu)
                                        <option value="{{ $pu->id }}">
                                            {{ $pu->programPelatihanUnit->independentCompetencyUnit->name ?? 'Unit #' . $pu->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Tipe Pengajar -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipe Pengajar <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-8">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe" required
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Internal</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="pengajar_tipe" value="eksternal" x-model="pengajarTipe"
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Eksternal</span>
                                    </label>
                                </div>
                            </div>
                            <!-- Pengajar Internal -->
                            <div x-show="pengajarTipe === 'internal'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Instruktur <span class="text-red-500">*</span>
                                </label>
                                <select name="pengajar_internal_id" :required="pengajarTipe === 'internal'"
                                        x-model="editFormData.pengajar_internal_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Instruktur --</option>
                                    @foreach(\App\Models\Instructor::orderBy('name')->get() as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Pengajar Eksternal -->
                            <div x-show="pengajarTipe === 'eksternal'" x-transition>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Pengajar Eksternal <span class="text-red-500">*</span>
                                </label>
                                <select name="pengajar_eksternal_id" :required="pengajarTipe === 'eksternal'"
                                        x-model="editFormData.pengajar_eksternal_id"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Pilih Pengajar --</option>
                                    @foreach(\App\Models\PengajarEksternal::orderBy('nama')->get() as $pe)
                                        <option value="{{ $pe->id }}">{{ $pe->nama }} ({{ $pe->instansi }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                            <button type="button" @click="editSubUnitModal = false"
                                    class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================= -->
    <!-- NESTED MODAL: DELETE CONFIRMATION -->
    <!-- ========================================= -->
    <div x-show="deleteModal" 
         style="display: none"
         class="fixed inset-0 z-[60] overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="deleteModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-bold text-center text-gray-900 mb-2">Hapus Assignment?</h3>
                <p class="text-center text-gray-600 text-sm mb-6">
                    Yakin ingin menghapus assignment untuk <strong x-text="deleteData.name"></strong>? 
                    Tindakan ini tidak dapat dibatalkan.
                </p>

                <form method="POST" 
                    :action="deleteType === 'program' 
                        ? '/admin/pengajar-programs/' + deleteData.id 
                        : '/admin/pengajar-sub-units/' + deleteData.id"
                    @submit.prevent="$el.submit(); setTimeout(() => location.reload(), 800)">
                    @csrf
                    @method('DELETE')
                    
                    <div class="flex space-x-3">
                        <button type="button" @click="deleteModal = false" 
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
    </div>
</div>
</div>

{{-- Auto-open modal if validation errors exist --}}
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const createButton = document.querySelector('[\\@click*="createModal = true"]');
            if (createButton) {
                createButton.click();
            }
        }, 100);
    });
</script>
@endif
@endsection
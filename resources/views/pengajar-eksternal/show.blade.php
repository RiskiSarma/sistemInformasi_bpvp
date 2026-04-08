@extends('layouts.app')

@section('title', 'Detail Pengajar Eksternal - ' . $pengajarEksternal->nama)

@section('content')
<div class="max-w-5xl mx-auto"
     x-data="{
        editProgramModal: false,
        editSubUnitModal: false,
        deleteModal: false,
        deleteType: '',
        editFormData: {},
        deleteData: {}
     }">

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.pengajar-eksternal.index') }}" 
           class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
        <div class="flex gap-3">
            <a href="{{ route('admin.pengajar-eksternal.schedule', $pengajarEksternal) }}" 
               class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2">
                Jadwal
            </a>
            <a href="{{ route('admin.pengajar-eksternal.edit', $pengajarEksternal) }}" 
               class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Data
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow border overflow-hidden">

        <!-- Header -->
        <div class="px-6 py-5 border-b bg-gray-50 flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center text-3xl font-bold">
                {{ substr($pengajarEksternal->nama, 0, 1) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ $pengajarEksternal->nama }}</h2>
                <p class="text-gray-600">{{ $pengajarEksternal->jabatan ?? '-' }} • {{ $pengajarEksternal->instansi }}</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="px-6 border-b">
            <nav class="flex">
                <button onclick="showTab('info')" id="tab-info" 
                        class="tab-button px-6 py-4 font-medium border-b-2 border-blue-600 text-blue-600">
                    Info Dasar
                </button>
                <button onclick="showTab('programs')" id="tab-programs" 
                        class="tab-button px-6 py-4 font-medium text-gray-500 hover:text-gray-700">
                    Program (Jenis Materi)
                </button>
                <button onclick="showTab('subunits')" id="tab-subunits" 
                        class="tab-button px-6 py-4 font-medium text-gray-500 hover:text-gray-700">
                    Sub Units
                </button>
            </nav>
        </div>

        <!-- TAB INFO DASAR -->
        <div id="content-info" class="tab-content p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-blue-600 text-xl">👤</span>
                        <h4 class="font-semibold text-gray-800">Data Pribadi</h4>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500">NIK</p>
                            <p class="font-medium">{{ $pengajarEksternal->nik ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">NIP</p>
                            <p class="font-medium">{{ $pengajarEksternal->nip ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-green-600 text-xl">🏢</span>
                        <h4 class="font-semibold text-gray-800">Data Institusi</h4>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500">Instansi</p>
                            <p class="font-medium">{{ $pengajarEksternal->instansi }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Jabatan</p>
                            <p class="font-medium">{{ $pengajarEksternal->jabatan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Alamat</p>
                            <p class="font-medium">{{ $pengajarEksternal->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-purple-600 text-xl">🎓</span>
                        <h4 class="font-semibold text-gray-800">Pendidikan</h4>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-500">Jenjang Pendidikan</p>
                            <p class="font-medium">{{ $pengajarEksternal->pendidikan->pendidikan ?? 'S1/D4' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kejuruan / Bidang Studi</p>
                            <p class="font-medium">{{ $pengajarEksternal->kejuruan_pendidikan ?? 'Belum diisi' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB PROGRAM -->
        <div id="content-programs" class="tab-content hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h4 class="font-semibold text-lg">Daftar Pengajar</h4>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $pengajarEksternal->programAssignments->count() }} pengajar program</p>
                </div>
            </div>

            @if($pengajarEksternal->programAssignments->count() > 0)
            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Materi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instansi</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($pengajarEksternal->programAssignments as $index => $ass)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $pengajarEksternal->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">Eksternal</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $ass->program->masterProgram->name ?? 'N/A' }}</div>
                                @if(!empty($ass->program->angkatan))
                                    <div class="text-xs text-gray-500">Angkatan {{ $ass->program->angkatan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                    {{ $ass->jenisMateri->jenis_materi_pelatihan ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $pengajarEksternal->instansi }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit -->
                                    <button @click="
                                        editFormData = {
                                            id: {{ $ass->id }},
                                            programs_id: {{ $ass->programs_id }},
                                            jenis_materi_pelatihan_id: {{ $ass->jenis_materi_pelatihan_id ?? 'null' }},
                                            pengajar_tipe: 'eksternal',
                                            pengajar_eksternal_id: {{ $pengajarEksternal->id }},
                                            pengajar_internal_id: null
                                        };
                                        editProgramModal = true;
                                    " class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <!-- Hapus -->
                                    <button @click="
                                        deleteData = { id: {{ $ass->id }}, name: '{{ addslashes($ass->program->masterProgram->name ?? 'Program') }}' };
                                        deleteType = 'program';
                                        deleteModal = true;
                                    " class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-12 text-gray-500">Belum ada program yang di-assign</div>
            @endif
        </div>

        <!-- TAB SUB UNITS -->
        <div id="content-subunits" class="tab-content hidden p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h4 class="font-semibold text-lg">Daftar Pengajar Sub Unit</h4>
                    <p class="text-sm text-gray-500 mt-1">Total: {{ $allSubUnitAssignments->count() }} pengajar sub unit</p>
                </div>
            </div>

            @if($allSubUnitAssignments->count() > 0)
            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($allSubUnitAssignments as $index => $sub)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">
                                {{ $sub->pengajarEksternalData?->nama ?? $sub->pengajarInternal?->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $sub->pengajar_eksternal === 'Y' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $sub->pengajar_eksternal === 'Y' ? 'Eksternal' : 'Internal' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">
                                    {{ $sub->paketPelatihanUnit?->programPelatihanUnit?->independentCompetencyUnit?->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $sub->program?->masterProgram?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit -->
                                    <button @click="
                                        editFormData = {
                                            id: {{ $sub->id }},
                                            programs_id: {{ $sub->programs_id ?? 'null' }},
                                            pp_unit_id: {{ $sub->pp_unit_id ?? 'null' }},
                                            pengajar_tipe: '{{ $sub->pengajar_eksternal === 'Y' ? 'eksternal' : 'internal' }}',
                                            pengajar_internal_id: {{ $sub->pengajar_internal_id ?? 'null' }},
                                            pengajar_eksternal_id: {{ $sub->pengajar_eksternal_id ?? 'null' }}
                                        };
                                        editSubUnitModal = true;
                                    " class="text-green-600 hover:text-green-800 transition" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <!-- Hapus -->
                                    <button @click="
                                        deleteData = { id: {{ $sub->id }}, name: '{{ addslashes($sub->paketPelatihanUnit?->programPelatihanUnit?->independentCompetencyUnit?->name ?? 'Sub Unit') }}' };
                                        deleteType = 'subunit';
                                        deleteModal = true;
                                    " class="text-red-600 hover:text-red-800 transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div class="text-center py-12 text-gray-500">Belum ada sub unit yang di-assign</div>
            @endif
        </div>

    </div>

    <!-- ========================================= -->
    <!-- MODAL EDIT PROGRAM ASSIGNMENT -->
    <!-- ========================================= -->
    <div x-show="editProgramModal"
         style="display: none"
         class="fixed inset-0 z-50 overflow-y-auto"
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
                          :action="'/admin/pengajar-programs/' + editFormData.id"
                          x-data="{ pengajarTipe: editFormData.pengajar_tipe || 'internal' }"
                          x-init="$watch('editFormData', val => pengajarTipe = val.pengajar_tipe || 'internal')">
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
                                        <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe"
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

                            <!-- Instruktur Internal -->
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

    <!-- ========================================= -->
    <!-- MODAL EDIT SUB UNIT ASSIGNMENT -->
    <!-- ========================================= -->
    <div x-show="editSubUnitModal"
         style="display: none"
         class="fixed inset-0 z-50 overflow-y-auto"
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
                          x-data="{ pengajarTipe: editFormData.pengajar_tipe || 'internal' }"
                          x-init="$watch('editFormData', val => pengajarTipe = val.pengajar_tipe || 'internal')">
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
                                        <input type="radio" name="pengajar_tipe" value="internal" x-model="pengajarTipe"
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

                            <!-- Instruktur Internal -->
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
    <!-- MODAL DELETE CONFIRMATION -->
    <!-- ========================================= -->
    <div x-show="deleteModal"
         style="display: none"
         class="fixed inset-0 z-50 overflow-y-auto"
         x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="deleteModal = false" class="fixed inset-0 bg-black bg-opacity-50"></div>
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
                <!-- Icon -->
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
                          : '/admin/pengajar-sub-units/' + deleteData.id">
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

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('content-' + tab).classList.remove('hidden');

    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
        btn.classList.add('text-gray-500');
    });
    const active = document.getElementById('tab-' + tab);
    active.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
    active.classList.remove('text-gray-500');
}
</script>
@endsection
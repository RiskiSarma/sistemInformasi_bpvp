@extends('layouts.app')

@section('title', 'Detail Pengajar Eksternal - ' . $pengajarEksternal->nama)

@section('content')
<div class="max-w-5xl mx-auto">

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
               class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Jadwal</a>
            <a href="{{ route('admin.pengajar-eksternal.edit', $pengajarEksternal) }}" 
               class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Edit Data</a>
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
                <h4 class="font-semibold text-lg">Daftar Pengajar</h4>
                <span class="text-sm text-gray-500">Total: {{ $pengajarEksternal->programAssignments->count() }} pengajar program</span>
            </div>

            @if($pengajarEksternal->programAssignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Tipe</th>
                            <th class="px-4 py-3 text-left">Program</th>
                            <th class="px-4 py-3 text-left">Jenis Materi</th>
                            <th class="px-4 py-3 text-left">Instansi</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($pengajarEksternal->programAssignments as $index => $ass)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium">{{ $pengajarEksternal->nama }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs rounded-full bg-orange-100 text-orange-700">Eksternal</span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $ass->program->masterProgram->name ?? 'N/A' }}
                                @if(!empty($ass->program->angkatan))
                                    <br><span class="text-xs text-gray-500">Angkatan {{ $ass->program->angkatan }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                                    {{ $ass->jenisMateri->jenis_materi_pelatihan ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $pengajarEksternal->instansi }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    {{-- Gunakan data-* attribute, HINDARI inline JS kompleks --}}
                                    <button type="button"
                                        data-action="edit"
                                        data-type="program"
                                        data-id="{{ $ass->id }}"
                                        data-programs-id="{{ $ass->programs_id }}"
                                        data-jenis-materi-id="{{ $ass->jenis_materi_pelatihan_id ?? '' }}"
                                        class="btn-edit text-emerald-600 hover:text-emerald-700 p-1.5 rounded hover:bg-emerald-50 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        data-action="delete"
                                        data-type="program"
                                        data-id="{{ $ass->id }}"
                                        data-name="{{ $ass->program->masterProgram->name ?? 'Program' }}"
                                        class="btn-delete text-red-600 hover:text-red-700 p-1.5 rounded hover:bg-red-50 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.595 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.595-1.858L5 7M9 7V4h6v3"/>
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
                <h4 class="font-semibold text-lg">Daftar Pengajar Sub Unit</h4>
                <span class="text-sm text-gray-500">Total: {{ $allSubUnitAssignments->count() }} pengajar sub unit</span>
            </div>

            @if($allSubUnitAssignments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Tipe</th>
                            <th class="px-4 py-3 text-left">Paket Unit</th>
                            <th class="px-4 py-3 text-left">Program</th>
                            <th class="px-4 py-3 text-left">Aksi</th>
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
                                <span class="px-3 py-1 text-xs rounded-full {{ $sub->pengajar_eksternal === 'Y' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ $sub->pengajar_eksternal === 'Y' ? 'Eksternal' : 'Internal' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $sub->paketPelatihanUnit?->programPelatihanUnit?->independentCompetencyUnit?->name ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3">{{ $sub->program?->masterProgram?->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <button type="button"
                                        data-action="edit"
                                        data-type="subunit"
                                        data-id="{{ $sub->id }}"
                                        class="btn-edit text-emerald-600 hover:text-emerald-700 p-1.5 rounded hover:bg-emerald-50 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button type="button"
                                        data-action="delete"
                                        data-type="subunit"
                                        data-id="{{ $sub->id }}"
                                        data-name="{{ $sub->paketPelatihanUnit?->programPelatihanUnit?->independentCompetencyUnit?->name ?? 'Sub Unit' }}"
                                        class="btn-delete text-red-600 hover:text-red-700 p-1.5 rounded hover:bg-red-50 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.595 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.595-1.858L5 7M9 7V4h6v3"/>
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
</div>

{{-- ===== MODAL EDIT PROGRAM ===== --}}
<div id="modal-edit-program" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" id="overlay-edit-program"></div>
        <div class="relative bg-white rounded-2xl max-w-xl w-full shadow-2xl z-10">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Edit Assignment Pengajar</h3>
                    <button id="btn-close-edit-program" type="button" 
                            class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form id="form-edit-program" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jenis Materi Pelatihan <span class="text-red-500">*</span>
                            </label>
                            <select name="jenis_materi_pelatihan_id" id="ep-jenis-materi" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Jenis Materi --</option>
                                @foreach($jenisMateriList ?? [] as $jm)
                                    <option value="{{ $jm->id }}">{{ $jm->jenis_materi_pelatihan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Pengajar <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tipe_pengajar_ep" value="internal" 
                                           class="ep-tipe-radio accent-blue-600" checked>
                                    <span class="text-sm">Internal</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tipe_pengajar_ep" value="eksternal" 
                                           class="ep-tipe-radio accent-blue-600">
                                    <span class="text-sm">Eksternal</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Instruktur <span class="text-red-500">*</span>
                            </label>
                            <select name="instruktur_id" id="ep-instruktur" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach($instrukturInternalList ?? [] as $ins)
                                    <option value="{{ $ins->id }}" data-tipe="internal">{{ $ins->name }}</option>
                                @endforeach
                                @foreach($instrukturEksternalList ?? [] as $ins)
                                    <option value="{{ $ins->id }}" data-tipe="eksternal">{{ $ins->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Program <span class="text-red-500">*</span>
                            </label>
                            <select name="programs_id" id="ep-program" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Program --</option>
                                @foreach($programList ?? [] as $prog)
                                    <option value="{{ $prog->id }}">
                                        {{ $prog->masterProgram->name ?? '' }}
                                        @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" id="btn-cancel-edit-program"
                                class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL EDIT SUB UNIT ===== --}}
<div id="modal-edit-subunit" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" id="overlay-edit-subunit"></div>
        <div class="relative bg-white rounded-2xl max-w-xl w-full shadow-2xl z-10">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Edit Assignment Sub Unit</h3>
                    <button id="btn-close-edit-subunit" type="button" 
                            class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>
                <form id="form-edit-subunit" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Program <span class="text-red-500">*</span>
                            </label>
                            <select name="programs_id" id="es-program" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Program --</option>
                                @foreach($programList ?? [] as $prog)
                                    <option value="{{ $prog->id }}">
                                        {{ $prog->masterProgram->name ?? '' }}
                                        @if($prog->angkatan) - Angkatan {{ $prog->angkatan }} @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Paket Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="paket_pelatihan_unit_id" id="es-paket-unit" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Paket Unit --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipe Pengajar <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-6 mt-1">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tipe_pengajar_es" value="internal" 
                                           class="es-tipe-radio accent-blue-600" checked>
                                    <span class="text-sm">Internal</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="tipe_pengajar_es" value="eksternal" 
                                           class="es-tipe-radio accent-blue-600">
                                    <span class="text-sm">Eksternal</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Instruktur <span class="text-red-500">*</span>
                            </label>
                            <select name="instruktur_id" id="es-instruktur" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Instruktur --</option>
                                @foreach($instrukturInternalList ?? [] as $ins)
                                    <option value="{{ $ins->id }}" data-tipe="internal">{{ $ins->name }}</option>
                                @endforeach
                                @foreach($instrukturEksternalList ?? [] as $ins)
                                    <option value="{{ $ins->id }}" data-tipe="eksternal">{{ $ins->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" id="btn-cancel-edit-subunit"
                                class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL DELETE ===== --}}
<div id="modal-delete" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" id="overlay-delete"></div>
        <div class="relative bg-white rounded-2xl max-w-md w-full shadow-2xl z-10">
            <div class="p-8 text-center">
                {{-- Warning Icon --}}
                <div class="flex justify-center mb-5">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Hapus Assignment?</h3>
                <p class="text-gray-500 mb-8">
                    Yakin ingin menghapus assignment untuk 
                    <strong id="delete-name-text" class="text-gray-800"></strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <form id="form-delete" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="flex gap-3">
                        <button type="button" id="btn-cancel-delete"
                                class="flex-1 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 font-medium text-gray-700">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── TAB ──────────────────────────────────────────────
    window.showTab = function(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('content-' + tab).classList.remove('hidden');

        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
            btn.classList.add('text-gray-500');
        });
        var active = document.getElementById('tab-' + tab);
        active.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
        active.classList.remove('text-gray-500');
    };

    // ── HELPERS ───────────────────────────────────────────
    function openModal(id)  { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.add('hidden');    document.body.style.overflow = ''; }

    // ── EDIT PROGRAM ──────────────────────────────────────
    function openEditProgram(btn) {
        var id         = btn.getAttribute('data-id');
        var programsId = btn.getAttribute('data-programs-id') || '';
        var jenisId    = btn.getAttribute('data-jenis-materi-id') || '';

        document.getElementById('form-edit-program').action = '/admin/pengajar-programs/' + id;

        var selJenis = document.getElementById('ep-jenis-materi');
        var selProg  = document.getElementById('ep-program');
        if (selJenis) selJenis.value = jenisId;
        if (selProg)  selProg.value  = programsId;

        openModal('modal-edit-program');
    }

    ['btn-close-edit-program','btn-cancel-edit-program'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function() { closeModal('modal-edit-program'); });
    });
    document.getElementById('overlay-edit-program')?.addEventListener('click', function() { closeModal('modal-edit-program'); });

    // ── EDIT SUBUNIT ──────────────────────────────────────
    function openEditSubunit(btn) {
        var id = btn.getAttribute('data-id');
        document.getElementById('form-edit-subunit').action = '/admin/pengajar-sub-units/' + id;
        openModal('modal-edit-subunit');
    }

    ['btn-close-edit-subunit','btn-cancel-edit-subunit'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('click', function() { closeModal('modal-edit-subunit'); });
    });
    document.getElementById('overlay-edit-subunit')?.addEventListener('click', function() { closeModal('modal-edit-subunit'); });

    // ── DELETE ────────────────────────────────────────────
    function openDelete(btn) {
        var type = btn.getAttribute('data-type');
        var id   = btn.getAttribute('data-id');
        var name = btn.getAttribute('data-name');

        document.getElementById('form-delete').action = type === 'program'
            ? '/admin/pengajar-programs/' + id
            : '/admin/pengajar-sub-units/' + id;

        document.getElementById('delete-name-text').textContent = name;
        openModal('modal-delete');
    }

    document.getElementById('btn-cancel-delete')?.addEventListener('click', function() { closeModal('modal-delete'); });
    document.getElementById('overlay-delete')?.addEventListener('click', function() { closeModal('modal-delete'); });

    // ── EVENT DELEGATION ──────────────────────────────────
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-edit');
        if (btn) {
            btn.getAttribute('data-type') === 'program'
                ? openEditProgram(btn)
                : openEditSubunit(btn);
            return;
        }
        btn = e.target.closest('.btn-delete');
        if (btn) { openDelete(btn); return; }
    });

    // ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('modal-edit-program');
            closeModal('modal-edit-subunit');
            closeModal('modal-delete');
        }
    });
});
</script>
@endsection
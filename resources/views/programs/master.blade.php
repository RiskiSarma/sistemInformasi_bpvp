@extends('layouts.app')

@section('title', 'Program Pelatihan (Master)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Program Pelatihan (Master)</h2>
            <p class="text-gray-600 mt-1">Kelola master data program pelatihan</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.programs.sync-kemnaker') }}" 
            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync dari Kemnaker
            </a>
        </div>
    </div>

    <!-- Form Tambah Master Program -->
    <div class="bg-white rounded-lg shadow-sm border p-6" x-data="{ showForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Master Program</h3>
            <button @click="showForm = !showForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <span x-show="!showForm">+ Tambah Baru</span>
                <span x-show="showForm">× Tutup</span>
            </button>
        </div>

        <form x-show="showForm" x-collapse method="POST" action="{{ route('admin.programs.master.store') }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror" placeholder="Contoh: MP001">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours') }}" required min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('duration_hours') border-red-500 @enderror">
                    @error('duration_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            


            <!-- Field baru: Kejuruan, Bidang, Jenis Pelatihan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="kejuruan" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
                    <select name="kejuruan" id="kejuruan" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('kejuruan') border-red-500 @enderror">
                        <option value="">-- Pilih Kejuruan --</option>
                        <option value="Bisnis dan Manajemen" {{ old('kejuruan') == 'Bisnis dan Manajemen' ? 'selected' : '' }}>Bisnis dan Manajemen</option>
                        <option value="Las" {{ old('kejuruan') == 'Las' ? 'selected' : '' }}>Las</option>
                        <option value="Fashion Technology" {{ old('kejuruan') == 'Fashion Technology' ? 'selected' : '' }}>Fashion Technology</option>
                        <option value="Konstruksi" {{ old('kejuruan') == 'Konstruksi' ? 'selected' : '' }}>Konstruksi</option>
                        <option value="Teknologi Informasi dan Komunikasi" {{ old('kejuruan') == 'Teknologi Informasi dan Komunikasi' ? 'selected' : '' }}>Teknologi Informasi dan Komunikasi</option>
                        <option value="Elektronika" {{ old('kejuruan') == 'Elektronika' ? 'selected' : '' }}>Elektronika</option>
                        <option value="Refrigerasi" {{ old('kejuruan') == 'Refrigerasi' ? 'selected' : '' }}>Refrigerasi</option>
                        <option value="Listrik" {{ old('kejuruan') == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Otomotif" {{ old('kejuruan') == 'Otomotif' ? 'selected' : '' }}>Otomotif</option>
                        <option value="Pariwisata" {{ old('kejuruan') == 'Pariwisata' ? 'selected' : '' }}>Pariwisata</option>
                        <option value="Tailor Made Training" {{ old('kejuruan') == 'Tailor Made Training' ? 'selected' : '' }}>Tailor Made Training</option>
                    </select>
                    @error('kejuruan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bidang" class="block text-sm font-medium text-gray-700 mb-1">Bidang <span class="text-red-500">*</span></label>
                    <select name="bidang" id="bidang" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('bidang') border-red-500 @enderror">
                        <option value="">-- Pilih Bidang --</option>
                        <option value="Bidang Industri dan Jasa" {{ old('bidang') == 'Bidang Industri dan Jasa' ? 'selected' : '' }}>Bidang Industri dan Jasa</option>
                        <option value="Bidang Pariwisata dan Industri Kreatif" {{ old('bidang') == 'Bidang Pariwisata dan Industri Kreatif' ? 'selected' : '' }}>Bidang Pariwisata dan Industri Kreatif</option>
                        <option value="Bidang Infrastruktur" {{ old('bidang') == 'Bidang Infrastruktur' ? 'selected' : '' }}>Bidang Infrastruktur</option>
                        <option value="Smart Creative IT Skills" {{ old('bidang') == 'Smart Creative IT Skills' ? 'selected' : '' }}>Smart Creative IT Skills</option>
                        <option value="Bidang Pariwisata dan Industri Kreatif" {{ old('bidang') == 'Bidang Pariwisata dan Industri Kreatif' ? 'selected' : '' }}>Bidang Pariwisata dan Industri Kreatif</option>
                        <option value="Bidang TIK" {{ old('bidang') == 'Bidang TIK' ? 'selected' : '' }}>Bidang TIK</option>
                        <option value="Bidang Green Job" {{ old('bidang') == 'Bidang Green Job' ? 'selected' : '' }}>Bidang Green Job</option>
                        <option value="Smart Office" {{ old('bidang') == 'Smart Office' ? 'selected' : '' }}>Smart Office</option>
                        <option value="Smart Farming" {{ old('bidang') == 'Smart Farming' ? 'selected' : '' }}>Smart Farming</option>
                        <option value="Smart Building" {{ old('bidang') == 'Smart Building' ? 'selected' : '' }}>Smart Building</option>
                        <option value="Smart Tourism" {{ old('bidang') == 'Smart Tourism' ? 'selected' : '' }}>Smart Tourism</option>
                    </select>
                    @error('bidang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- <div>
                    <label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelatihan <span class="text-red-500">*</span></label>
                    <select name="jenis_pelatihan" id="jenis_pelatihan" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('jenis_pelatihan') border-red-500 @enderror">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Non Boarding" {{ old('jenis_pelatihan') == 'Non Boarding' ? 'selected' : '' }}>Non Boarding</option>
                        <option value="Project Based Learning (PBL)" {{ old('jenis_pelatihan') == 'Project Based Learning (PBL)' ? 'selected' : '' }}>Project Based Learning (PBL)</option>
                        <option value="Boarding" {{ old('jenis_pelatihan') == 'Boarding' ? 'selected' : '' }}>Boarding</option>
                        <option value="Tailor Made Training" {{ old('jenis_pelatihan') == 'Tailor Made Training' ? 'selected' : '' }}>Tailor Made Training</option>
                        <option value="PFLK" {{ old('jenis_pelatihan') == 'PFLK' ? 'selected' : '' }}>PFLK</option>
                    </select>
                    @error('jenis_pelatihan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div> --}}
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Program Aktif</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <button type="button" @click="showForm = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Master Program
                </button>
            </div>
        </form>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex items-center space-x-4">
            <input type="text" name="search" placeholder="Cari program..." value="{{ request('search') }}" class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Master Programs List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-48">Kode</th> <!-- tambah w-48 atau w-64 -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kejuruan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang</th>
                        {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Pelatihan</th> --}}
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($masterPrograms as $mp)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-normal break-all text-sm font-mono text-gray-900">
                            <span class="font-mono text-sm font-medium text-gray-900">{{ $mp->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $mp->name }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $mp->kejuruan ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $mp->bidang ?? '-' }}</td>
                        {{-- <td class="px-6 py-4">{{ $mp->jenis_pelatihan_full }}</td> --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $mp->duration_hours }} jam
                        </td>
                        {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $mp->competencyUnits->count() }} unit
                        </td> --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $mp->programs->sum(fn($program) => $program->independentCompetencyUnits->count()) }} unit
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $mp->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $mp->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.programs.master.show', $mp) }}" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.programs.master.edit', $mp) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.programs.master.destroy', $mp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus master program ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            Belum ada master program
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($masterPrograms->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $masterPrograms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
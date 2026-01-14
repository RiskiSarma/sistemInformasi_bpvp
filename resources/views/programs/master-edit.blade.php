@extends('layouts.app')

@section('title', 'Edit Master Program')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.programs.master') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Master Program</h2>

        <form method="POST" action="{{ route('admin.programs.master.update', $masterProgram) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $masterProgram->code) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours', $masterProgram->duration_hours) }}" required min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('duration_hours') border-red-500 @enderror">
                    @error('duration_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Program <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $masterProgram->name) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Field baru -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="kejuruan" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
                    <select name="kejuruan" id="kejuruan" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('kejuruan') border-red-500 @enderror">
                        <option value="">-- Pilih Kejuruan --</option>
                        <option value="Bidan dan Manajemen" {{ old('kejuruan', $masterProgram->kejuruan) == 'Bidan dan Manajemen' ? 'selected' : '' }}>Bidan dan Manajemen</option>
                        <option value="Las" {{ old('kejuruan', $masterProgram->kejuruan) == 'Las' ? 'selected' : '' }}>Las</option>
                        <option value="Fashion Technology" {{ old('kejuruan', $masterProgram->kejuruan) == 'Fashion Technology' ? 'selected' : '' }}>Fashion Technology</option>
                        <option value="Konstruksi" {{ old('kejuruan', $masterProgram->kejuruan) == 'Konstruksi' ? 'selected' : '' }}>Konstruksi</option>
                        <option value="Teknologi Informasi dan Komunikasi" {{ old('kejuruan', $masterProgram->kejuruan) == 'Teknologi Informasi dan Komunikasi' ? 'selected' : '' }}>Teknologi Informasi dan Komunikasi</option>
                        <option value="Elektronika" {{ old('kejuruan', $masterProgram->kejuruan) == 'Elektronika' ? 'selected' : '' }}>Elektronika</option>
                        <option value="Refrigerasi" {{ old('kejuruan', $masterProgram->kejuruan) == 'Refrigerasi' ? 'selected' : '' }}>Refrigerasi</option>
                        <option value="Listrik" {{ old('kejuruan', $masterProgram->kejuruan) == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                        <option value="Otomotif" {{ old('kejuruan', $masterProgram->kejuruan) == 'Otomotif' ? 'selected' : '' }}>Otomotif</option>
                        <option value="Pariwisata" {{ old('kejuruan', $masterProgram->kejuruan) == 'Pariwisata' ? 'selected' : '' }}>Pariwisata</option>
                        <option value="Tailor Made Training" {{ old('kejuruan', $masterProgram->kejuruan) == 'Tailor Made Training' ? 'selected' : '' }}>Tailor Made Training</option>
                        <!-- Tambahkan opsi lain sesuai data Excel kamu -->
                    </select>
                    @error('kejuruan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bidang" class="block text-sm font-medium text-gray-700 mb-1">Bidang <span class="text-red-500">*</span></label>
                    <select name="bidang" id="bidang" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('bidang') border-red-500 @enderror">
                        <option value="">-- Pilih Bidang --</option>
                        <option value="Bidang Industri dan Jasa" {{ old('bidang', $masterProgram->bidang) == 'Bidang Industri dan Jasa' ? 'selected' : '' }}>Bidang Industri dan Jasa</option>
                        <option value="Bidang Pariwisata dan Industri Kreatif" {{ old('bidang', $masterProgram->bidang) == 'Bidang Pariwisata dan Industri Kreatif' ? 'selected' : '' }}>Bidang Pariwisata dan Industri Kreatif</option>
                        <option value="Bidang Infrastruktur" {{ old('bidang', $masterProgram->bidang) == 'Bidang Infrastruktur' ? 'selected' : '' }}>Bidang Infrastruktur</option>
                        <option value="Smart Creative IT Skill" {{ old('bidang', $masterProgram->bidang) == 'Smart Creative IT Skills' ? 'selected' : '' }}>Smart Creative IT Skills</option>
                        <option value="Bidang TIK" {{ old('bidang', $masterProgram->bidang) == 'Bidang TIK' ? 'selected' : '' }}>Bidang TIK</option>
                        <option value="Bidang Green Job" {{ old('bidang', $masterProgram->bidang) == 'Bidang Green Job' ? 'selected' : '' }}>Bidang Green Job</option>
                        <option value="Smart Office" {{ old('bidang', $masterProgram->bidang) == 'Smart Office' ? 'selected' : '' }}>Smart Office</option>
                        <option value="Smart Farming" {{ old('bidang', $masterProgram->bidang) == 'Smart Farming' ? 'selected' : '' }}>Smart Farming</option>
                        <option value="Smart Building" {{ old('bidang', $masterProgram->bidang) == 'Smart Building' ? 'selected' : '' }}>Smart Building</option>
                        <option value="Smart Tourism" {{ old('bidang', $masterProgram->bidang) == 'Smart Tourism' ? 'selected' : '' }}>Smart Tourism</option>
                        <!-- Tambahkan opsi lain sesuai Excel -->
                    </select>
                    @error('bidang')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="jenis_pelatihan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Pelatihan <span class="text-red-500">*</span></label>
                    <select name="jenis_pelatihan" id="jenis_pelatihan" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('jenis_pelatihan') border-red-500 @enderror">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Non Boarding" {{ old('jenis_pelatihan', $masterProgram->jenis_pelatihan) == 'Non Boarding' ? 'selected' : '' }}>Non Boarding</option>
                        <option value="PBL" {{ old('jenis_pelatihan', $masterProgram->jenis_pelatihan) == 'PBL' ? 'selected' : '' }}>Project Based Learning (PBL)</option>
                        <option value="Tailor Made Training" {{ old('jenis_pelatihan', $masterProgram->jenis_pelatihan) == 'Tailor Made Training' ? 'selected' : '' }}>Tailor Made Training</option>
                        <option value="PFLK" {{ old('jenis_pelatihan', $masterProgram->jenis_pelatihan) == 'PFLK' ? 'selected' : '' }}>PFLK</option>
                        <!-- Tambahkan opsi lain jika ada -->
                    </select>
                    @error('jenis_pelatihan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $masterProgram->description) }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $masterProgram->is_active) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Program Aktif</label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.programs.master') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Master Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
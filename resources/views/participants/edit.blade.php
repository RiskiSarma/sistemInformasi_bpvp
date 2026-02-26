@extends('layouts.app')

@section('title', 'Edit Peserta')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.participants.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Daftar Peserta</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Data Peserta</h2>

        <form method="POST" action="{{ route('admin.participants.update', $participant) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">Program <span class="text-red-500">*</span></label>
                <select name="program_id" id="program_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-500 @enderror">
                    <option value="">Pilih Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', $participant->program_id) == $program->id ? 'selected' : '' }}>
                            {{ $program->masterProgram?->name ?? 'Program Tidak Diketahui' }}
                            @if($program->paketPelatihan)
                                - {{ $program->paketPelatihan->nama_batch ?? $program->paketPelatihan->batch ?? $program->paketPelatihan->code ?? 'Batch Tidak Diketahui' }}
                            @else
                                - Batch Tidak Diketahui
                            @endif
                            {{ $program->masterProgram?->angkatan ? ' (' . $program->masterProgram->angkatan . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="gender" id="gender" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki" {{ old('gender', $participant->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender', $participant->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" value="{{ $participant->user->name }}" disabled class="w-full px-3 py-2 border bg-gray-50 rounded-lg">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $participant->user->email }}" disabled class="w-full px-3 py-2 border bg-gray-50 rounded-lg">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $participant->phone) }}" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK (Opsional)</label>
                <input 
                    type="text" 
                    name="nik" 
                    id="nik" 
                    value="{{ old('nik', $participant->nik) }}" 
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('nik') border-red-500 @enderror"
                    placeholder="16 digit NIK"
                    maxlength="16"
                >
                @error('nik')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">NIK unik dan bisa diubah jika salah isi saat daftar.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $participant->birth_place) }}"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Kota / Kabupaten">
                    @error('birth_place') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" 
                        value="{{ old('birth_date', $participant->birth_date ? $participant->birth_date->format('Y-m-d') : '') }}"
                        class="w-full px-3 py-2 border rounded-lg">
                    @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="pendidikan_id" class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                <select name="pendidikan_id" id="pendidikan_id" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('pendidikan_id') border-red-500 @enderror">
                    <option value="">Pilih Pendidikan</option>
                    @foreach(\App\Models\Pendidikan::orderBy('pendidikan')->get() as $pend)
                        <option value="{{ $pend->id }}" {{ old('pendidikan_id', $participant->pendidikan_id) == $pend->id ? 'selected' : '' }}>
                            {{ $pend->pendidikan }}
                        </option>
                    @endforeach
                </select>
                @error('pendidikan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('address', $participant->address) }}</textarea>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                    <option value="active" {{ old('status', $participant->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="graduated" {{ old('status', $participant->status) == 'graduated' ? 'selected' : '' }}>Lulus</option>
                    <option value="dropout" {{ old('status', $participant->status) == 'dropout' ? 'selected' : '' }}>Dropout</option>
                </select>
                @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.participants.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
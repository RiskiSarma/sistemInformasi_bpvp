@extends('layouts.app')

@section('title', 'Tambah Jadwal Mengajar')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.pengajar-eksternal.schedule', $pengajarEksternal) }}" 
           class="text-blue-600 hover:text-blue-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tambah Jadwal Mengajar</h2>
            <p class="text-gray-600 mt-1">{{ $pengajarEksternal->nama }}</p>
        </div>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="text-red-800 font-semibold mb-2">Terjadi kesalahan:</h3>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.pengajar-eksternal.schedules.store', $pengajarEksternal) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Program -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Program <span class="text-red-500">*</span></label>
                <select name="program_id" required 
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-500 @enderror">
                    <option value="">Pilih Program - Batch</option>
                    
                    @forelse($programs as $program)
                        <option value="{{ $program->id }}" 
                                {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            
                            {{-- Nama Program dari Master Program --}}
                            {{ $program->masterProgram?->name ?? 'Nama Program Tidak Diketahui' }}
                            
                            {{-- Batch Information --}}
                            @if($program->batch)
                                - Batch {{ $program->batch }}
                            @elseif($program->paketPelatihan?->batch)
                                - Batch {{ $program->paketPelatihan?->batch }}
                            @elseif($program->paketPelatihan?->periode)
                                - {{ $program->paketPelatihan?->periode }}
                            @else
                                - Batch -
                            @endif
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada program tersedia</option>
                    @endforelse
                </select>
                @error('program_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Day of Week -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Hari <span class="text-red-500">*</span>
                </label>
                <select name="day_of_week" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('day_of_week') border-red-500 @enderror">
                    <option value="">-- Pilih Hari --</option>
                    @foreach($days as $key => $day)
                        <option value="{{ $key }}" {{ old('day_of_week') == $key ? 'selected' : '' }}>
                            {{ $day }}
                        </option>
                    @endforeach
                </select>
                @error('day_of_week')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" step="60" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" step="60" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('end_time') border-red-500 @enderror">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Room -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ruangan
                </label>
                <input type="text" name="room" value="{{ old('room') }}" 
                       placeholder="Contoh: Workshop, Lab Komputer, Ruang Kelas 1"
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('room') border-red-500 @enderror">
                @error('room')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea name="notes" rows="3" 
                          placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Jadwal Aktif
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.pengajar-eksternal.schedule', $pengajarEksternal) }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Tips:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Pastikan waktu mengajar tidak bentrok dengan jadwal lain</li>
                    <li>Format waktu menggunakan 24 jam (Contoh: 08:00, 13:30)</li>
                    <li>Jadwal yang tidak aktif tidak akan muncul di daftar jadwal mingguan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
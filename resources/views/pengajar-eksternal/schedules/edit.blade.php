@extends('layouts.app')

@section('title', 'Edit Jadwal Mengajar')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.pengajar-eksternal.schedule', $schedule->pengajarEksternal) }}" 
           class="text-blue-600 hover:text-blue-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Jadwal Mengajar</h2>
            <p class="text-gray-600 mt-1">{{ $schedule->pengajarEksternal->nama }}</p>
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
        <form action="{{ route('admin.pengajar-eksternal.schedules.update', $schedule) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Program -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Program <span class="text-red-500">*</span>
                </label>
                <select name="program_id" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-500 @enderror">
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" 
                                {{ old('program_id', $schedule->program_id) == $program->id ? 'selected' : '' }}>
                            {{ $program->masterProgram->name ?? 'Program' }}
                            @if($program->angkatan)
                                - Angkatan {{ $program->angkatan }}
                            @endif
                        </option>
                    @endforeach
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
                        <option value="{{ $key }}" 
                                {{ old('day_of_week', $schedule->day_of_week) == $key ? 'selected' : '' }}>
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
                    <input type="time" name="start_time" 
                        value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}" 
                        step="60" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" 
                        value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}" 
                        step="60" required
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
                <input type="text" name="room" value="{{ old('room', $schedule->room) }}" 
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
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $schedule->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                       {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">
                    Jadwal Aktif
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.pengajar-eksternal.schedule', $schedule->pengajarEksternal) }}" 
                   class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Perbarui Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Button -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start justify-between">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800">Zona Bahaya</h3>
                    <p class="text-sm text-red-700 mt-1">Hapus jadwal ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <form action="{{ route('admin.pengajar-eksternal.schedules.destroy', $schedule) }}" 
                  method="POST" 
                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini? Tindakan ini tidak dapat dibatalkan!')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
                    Hapus Jadwal
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
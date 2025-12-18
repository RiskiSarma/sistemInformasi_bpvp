@extends('layouts.app')

@section('title', 'Terbitkan Sertifikat')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.certificates.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Terbitkan Sertifikat</h2>

        <form action="{{ route('admin.certificates.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Participant Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peserta <span class="text-red-500">*</span></label>
                    <select name="participant_id" id="participantSelect" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('participant_id') border-red-500 @enderror">
                        <option value="">-- Pilih Peserta --</option>
                        @foreach($participants as $participant)
                        <option value="{{ $participant->id }}" 
                                data-program="{{ $participant->program->masterProgram->name ?? 'N/A' }}"
                                data-batch="{{ $participant->program->batch ?? '' }}"
                                data-attendance="{{ $participant->getAttendancePercentage() }}"
                                {{ old('participant_id') == $participant->id ? 'selected' : '' }}>
                            {{ $participant->name }} - {{ $participant->program->masterProgram->name ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                    @error('participant_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    @if($participants->isEmpty())
                    <p class="mt-2 text-sm text-yellow-600">
                        <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Tidak ada peserta yang memenuhi syarat. Peserta harus berstatus "Lulus" dan belum memiliki sertifikat.
                    </p>
                    @endif
                </div>

                <!-- Participant Info (Hidden by default) -->
                <div id="participantInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Informasi Peserta</h3>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-medium">Program:</span> <span id="infoProgram">-</span></p>
                        <p><span class="font-medium">Batch:</span> <span id="infoBatch">-</span></p>
                        <p><span class="font-medium">Kehadiran:</span> <span id="infoAttendance">-</span>%</p>
                    </div>
                </div>

                <!-- Certificate Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Sertifikat <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500 font-normal">(Sesuai format resmi lembaga)</span>
                    </label>
                    <input type="text" 
                           name="certificate_number" 
                           id="certificateNumber"
                           value="{{ old('certificate_number', $suggestedNumber) }}" 
                           required 
                           placeholder="Contoh: BPVP/2025/CERT/0001"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('certificate_number') border-red-500 @enderror">
                    @error('certificate_number')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Saran: {{ $suggestedNumber }} (Anda bisa mengubah sesuai format resmi)
                    </p>
                </div>

                <!-- Issue Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('issue_date') border-red-500 @enderror">
                    @error('issue_date')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Requirements Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Syarat Sertifikat
                    </h4>
                    <ul class="text-sm text-gray-700 space-y-1 ml-7">
                        <li>• Status peserta: <strong>Lulus (Graduated)</strong></li>
                        <li>• Kehadiran minimal: <strong>75%</strong></li>
                        <li>• Belum pernah menerima sertifikat</li>
                        <li>• Nomor sertifikat harus unik dan sesuai format resmi lembaga</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="{{ route('admin.certificates.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Terbitkan Sertifikat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const participantSelect = document.getElementById('participantSelect');
    const participantInfo = document.getElementById('participantInfo');
    const infoProgram = document.getElementById('infoProgram');
    const infoBatch = document.getElementById('infoBatch');
    const infoAttendance = document.getElementById('infoAttendance');

    participantSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            const program = selectedOption.getAttribute('data-program');
            const batch = selectedOption.getAttribute('data-batch');
            const attendance = selectedOption.getAttribute('data-attendance');

            infoProgram.textContent = program;
            infoBatch.textContent = batch || '-';
            infoAttendance.textContent = attendance;

            participantInfo.classList.remove('hidden');
        } else {
            participantInfo.classList.add('hidden');
        }
    });

    // Trigger on page load if old value exists
    if (participantSelect.value) {
        participantSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
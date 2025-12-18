@extends('layouts.app')

@section('title', 'Terbitkan Sertifikat Massal')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.certificates.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Terbitkan Sertifikat Massal</h2>
                <p class="text-gray-600 mt-1">Masukkan nomor sertifikat untuk setiap peserta</p>
            </div>
        </div>

        <!-- Filter by Program -->
        <div class="mb-6 bg-gray-50 rounded-lg p-4">
            <form method="GET" class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Program</label>
                    <select name="program_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                        <option value="">Semua Program</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->masterProgram->name ?? $program->name }} - {{ $program->batch }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <form action="{{ route('admin.certificates.bulk-store') }}" method="POST" id="bulkForm">
            @csrf

            <!-- Issue Date -->
            <div class="mb-6 bg-gray-50 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Participants List -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Daftar Peserta dan Nomor Sertifikat <span class="text-red-500">*</span>
                        <span class="text-gray-500 font-normal">({{ $participants->count() }} peserta tersedia)</span>
                    </label>
                    <div class="space-x-2">
                        <button type="button" onclick="selectAll()" class="text-sm text-blue-600 hover:text-blue-800">Pilih Semua</button>
                        <button type="button" onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800">Batal Pilih</button>
                        <button type="button" onclick="autoNumbering()" class="text-sm text-teal-600 hover:text-teal-800">Auto Numbering</button>
                    </div>
                </div>

                @if($participants->isEmpty())
                <div class="text-center py-12 bg-yellow-50 rounded-lg border border-yellow-200">
                    <svg class="w-12 h-12 mx-auto text-yellow-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-gray-700 font-medium">Tidak ada peserta yang memenuhi syarat</p>
                    <p class="text-sm text-gray-600 mt-2">Peserta harus berstatus "Lulus", kehadiran minimal 75%, dan belum memiliki sertifikat.</p>
                    @if(request('program_id'))
                    <a href="{{ route('admin.certificates.bulk-create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Tampilkan Semua Program →
                    </a>
                    @endif
                </div>
                @else
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll(this)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($participants as $index => $participant)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" 
                                           name="participant_ids[]" 
                                           value="{{ $participant->id }}" 
                                           class="participant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           onchange="updateSelectedCount()">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $participant->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $participant->email }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900">{{ $participant->program->masterProgram->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500">{{ $participant->program->batch ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" 
                                           name="certificate_numbers[{{ $index }}]" 
                                           placeholder="BPVP/2025/CERT/{{ str_pad($index + 1, 4, '0', STR_PAD_LEFT) }}"
                                           class="certificate-number-input w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500"
                                           data-index="{{ $index }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Info Box -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    Informasi
                </h4>
                <ul class="text-sm text-gray-700 space-y-1 ml-7">
                    <li>• Centang peserta yang akan diterbitkan sertifikat</li>
                    <li>• Masukkan nomor sertifikat untuk setiap peserta (harus unik)</li>
                    <li>• Gunakan "Auto Numbering" untuk generate nomor otomatis</li>
                    <li>• PDF sertifikat akan otomatis di-generate</li>
                </ul>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('admin.certificates.index') }}" class="px-6 py-2 border rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition" 
                        onclick="return confirmBulk()">
                    <span id="submitText">Terbitkan Sertifikat</span>
                    <span id="selectedCount" class="ml-2 text-sm"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let startNumber = 1;

function toggleAll(checkbox) {
    const checkboxes = document.querySelectorAll('.participant-checkbox');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
    updateSelectedCount();
}

function selectAll() {
    const checkboxes = document.querySelectorAll('.participant-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    document.getElementById('selectAllCheckbox').checked = true;
    updateSelectedCount();
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.participant-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelectedCount();
}

function autoNumbering() {
    const year = new Date().getFullYear();
    const prefix = prompt('Masukkan prefix nomor sertifikat:', `BPVP/${year}/CERT/`);
    
    if (!prefix) return;
    
    const startNum = prompt('Mulai dari nomor:', '1');
    if (!startNum) return;
    
    let counter = parseInt(startNum);
    const inputs = document.querySelectorAll('.certificate-number-input');
    
    inputs.forEach(input => {
        const paddedNum = String(counter).padStart(4, '0');
        input.value = `${prefix}${paddedNum}`;
        counter++;
    });
    
    alert('Nomor sertifikat berhasil di-generate!');
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.participant-checkbox:checked').length;
    const countElement = document.getElementById('selectedCount');
    if (checked > 0) {
        countElement.textContent = `(${checked} dipilih)`;
    } else {
        countElement.textContent = '';
    }
}

function confirmBulk() {
    const checked = document.querySelectorAll('.participant-checkbox:checked');
    
    if (checked.length === 0) {
        alert('Silakan pilih minimal 1 peserta');
        return false;
    }
    
    // Validasi nomor sertifikat untuk peserta yang dipilih
    let hasEmptyNumber = false;
    let duplicates = [];
    let numbers = [];
    
    checked.forEach(cb => {
        const row = cb.closest('tr');
        const input = row.querySelector('.certificate-number-input');
        const name = row.querySelector('.font-medium').textContent;
        const number = input.value.trim();
        
        if (!number) {
            hasEmptyNumber = true;
        } else {
            if (numbers.includes(number)) {
                duplicates.push(number);
            }
            numbers.push(number);
        }
    });
    
    if (hasEmptyNumber) {
        alert('Semua peserta yang dipilih harus memiliki nomor sertifikat!');
        return false;
    }
    
    if (duplicates.length > 0) {
        alert('Nomor sertifikat duplikat: ' + duplicates.join(', '));
        return false;
    }
    
    return confirm(`Anda akan menerbitkan ${checked.length} sertifikat. Lanjutkan?`);
}

// Update count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endsection
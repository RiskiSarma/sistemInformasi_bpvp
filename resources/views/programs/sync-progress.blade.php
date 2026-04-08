@extends('layouts.app')
@section('title', 'Sinkronisasi Kemnaker')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Sinkronisasi Data Kemnaker</h2>
        <p class="text-gray-600 mt-1">Proses berjalan otomatis, jangan tutup halaman ini.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 space-y-4">
        <!-- Mode selector -->
        <div id="mode-selector" class="space-y-3">
            <p class="font-medium text-gray-700">Pilih mode sinkronisasi:</p>
            <div class="flex gap-3">
                <button onclick="startSync('update-null')"
                    class="flex-1 px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-center">
                    <div class="font-semibold">Update Data Kosong</div>
                    <div class="text-xs mt-1 opacity-90">Isi kolom kejuruan & bidang yang masih kosong</div>
                </button>
                <button onclick="startSync('full')"
                    class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-center">
                    <div class="font-semibold">Full Sync</div>
                    <div class="text-xs mt-1 opacity-90">Ambil semua program baru dari Kemnaker</div>
                </button>
            </div>
        </div>

        <!-- Progress area (hidden awalnya) -->
        <div id="progress-area" class="hidden space-y-4">
            <div class="flex items-center justify-between text-sm">
                <span id="progress-label" class="text-gray-600">Memulai...</span>
                <span id="progress-percent" class="font-semibold text-blue-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
            <div id="progress-log" class="bg-gray-50 rounded-lg p-3 h-40 overflow-y-auto text-xs text-gray-600 font-mono space-y-1">
            </div>
        </div>

        <!-- Done area (hidden awalnya) -->
        <div id="done-area" class="hidden text-center py-4 space-y-3">
            <svg class="w-16 h-16 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg font-semibold text-green-700">Sinkronisasi Selesai!</p>
            <p id="done-message" class="text-gray-600 text-sm"></p>
            <a href="{{ route('admin.programs.master') }}" 
               class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Kembali ke Daftar Program
            </a>
        </div>

        <!-- Error area -->
        <div id="error-area" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-700 font-medium">Terjadi kesalahan:</p>
            <p id="error-message" class="text-red-600 text-sm mt-1"></p>
            <button onclick="location.reload()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded-lg text-sm">
                Coba Lagi
            </button>
        </div>
    </div>
</div>

<script>
let totalUpdated = 0;

function addLog(message) {
    const log = document.getElementById('progress-log');
    const line = document.createElement('div');
    line.textContent = '[' + new Date().toLocaleTimeString() + '] ' + message;
    log.appendChild(line);
    log.scrollTop = log.scrollHeight;
}

function updateProgress(percent, label) {
    document.getElementById('progress-bar').style.width = percent + '%';
    document.getElementById('progress-percent').textContent = percent + '%';
    document.getElementById('progress-label').textContent = label;
}

async function startSync(mode) {
    document.getElementById('mode-selector').classList.add('hidden');
    document.getElementById('progress-area').classList.remove('hidden');

    addLog('Memulai sinkronisasi mode: ' + mode);

    if (mode === 'update-null') {
        await runUpdateNull(0);
    } else {
        await runFullSync(1);
    }
}

async function runUpdateNull(offset) {
    try {
        const response = await fetch('{{ route("admin.programs.sync-kemnaker") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            // Kirim params via URL
        });
        
        // Pakai URLSearchParams
        const url = new URL('{{ route("admin.programs.sync-kemnaker") }}');
        url.searchParams.set('mode', 'update-null');
        url.searchParams.set('offset', offset);

        const res = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        const data = await res.json();

        if (data.error) {
            showError(data.message);
            return;
        }

        totalUpdated += (data.updated || 0);
        updateProgress(data.progress || 0, data.message);
        addLog(data.message + ' | Diupdate: ' + (data.updated || 0));

        if (data.done) {
            showDone('Total ' + totalUpdated + ' program berhasil diupdate dari ' + data.total + ' yang dicek.');
        } else {
            // Lanjut chunk berikutnya
            setTimeout(() => runUpdateNull(data.next_offset), 500);
        }

    } catch (err) {
        showError('Network error: ' + err.message);
    }
}

async function runFullSync(page) {
    try {
        const url = new URL('{{ route("admin.programs.sync-kemnaker") }}');
        url.searchParams.set('mode', 'full');
        url.searchParams.set('page', page);

        const res = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        const data = await res.json();

        if (data.error) {
            showError(data.message);
            return;
        }

        updateProgress(Math.min(data.progress || 0, 99), data.message);
        addLog('Halaman ' + page + ' selesai');

        if (data.done) {
            showDone('Full sync selesai!');
        } else {
            setTimeout(() => runFullSync(data.next_page), 1000);
        }

    } catch (err) {
        showError('Network error: ' + err.message);
    }
}

function showDone(message) {
    updateProgress(100, 'Selesai!');
    document.getElementById('progress-bar').classList.remove('bg-blue-600');
    document.getElementById('progress-bar').classList.add('bg-green-500');
    document.getElementById('done-area').classList.remove('hidden');
    document.getElementById('done-message').textContent = message;
    addLog('✓ ' + message);
}

function showError(message) {
    document.getElementById('error-area').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
    addLog('✗ Error: ' + message);
}
</script>
@endsection
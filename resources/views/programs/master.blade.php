@extends('layouts.app')

@section('title', 'Program Pelatihan (Master)')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Program Pelatihan (Master)</h2>
            <p class="text-gray-600 mt-1">Kelola master data program pelatihan</p>
        </div>
        <div class="flex items-center space-x-3">
            {{-- Tombol AJAX - tidak redirect, tidak reload halaman --}}
            <button type="button"
               id="btn-update-null"
               onclick="triggerSync('update-null')"
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center"
               title="Isi kolom kosong (kejuruan, bidang) dari API Kemnaker">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Isi Data Kosong
            </button>

            <button type="button"
               id="btn-full-sync"
               onclick="triggerSync('full')"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center"
               title="Tarik semua program baru dari API Kemnaker">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync dari Kemnaker
            </button>
        </div>
    </div>

    {{-- ============================================================
         BANNER SYNC
         ============================================================ --}}
    <div id="sync-status-banner" class="hidden">

        {{-- Loading --}}
        <div id="sync-loading" class="hidden bg-white border border-blue-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 0v4a8 8 0 00-8 8H0z"></path>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Sinkronisasi berjalan...</span>
                    </div>
                    <span class="text-xs text-gray-400 hidden sm:block">Halaman bisa ditutup, proses tetap berlanjut</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div id="sync-progress-bar"
                         class="h-2 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 transition-all duration-500 ease-out"
                         style="width: 0%">
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-xs text-gray-500" id="sync-progress-label">Menghubungkan ke API Kemnaker...</span>
                    <span class="text-xs font-medium text-blue-600" id="sync-progress-pct"></span>
                </div>
            </div>
            <div class="h-0.5 bg-gradient-to-r from-transparent via-blue-300 to-transparent animate-pulse"></div>
        </div>

        {{-- Done --}}
        <div id="sync-done" class="hidden bg-green-50 border border-green-200 rounded-xl px-5 py-4">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-green-800">Sinkronisasi selesai!</p>
                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5" id="sync-stats"></div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                    <button onclick="location.reload()"
                            class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition font-medium">
                        Refresh Data
                    </button>
                    <button onclick="closeSyncBanner()" class="text-green-400 hover:text-green-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Error --}}
        <div id="sync-error" class="hidden bg-red-50 border border-red-200 rounded-xl px-5 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-red-700" id="sync-error-msg">Gagal memulai sinkronisasi. Coba lagi.</p>
                </div>
                <button onclick="closeSyncBanner()" class="text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

    </div>
    {{-- ============================================================ --}}

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">
        {{ session('error') }}
    </div>
    @endif

    <!-- Form Tambah Master Program -->
    <div class="bg-white rounded-lg shadow-sm border p-6" x-data="{ showForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Tambah Master Program</h3>
            <button @click="showForm = !showForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <span x-show="!showForm">+ Tambah Baru</span>
                <span x-show="showForm">× Tutup</span>
            </button>
        </div>

        <form x-show="showForm" x-collapse method="POST" action="{{ route('admin.programs.master.store') }}" class="space-y-6" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Program <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" required 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Program <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kejuruan_id" class="block text-sm font-medium text-gray-700 mb-1">Kejuruan <span class="text-red-500">*</span></label>
                    <select name="kejuruan_id" id="kejuruan_id" required 
                            class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kejuruan --</option>
                        @foreach(App\Models\Kejuruan::all() as $kejuruan)
                            <option value="{{ $kejuruan->id }}" {{ old('kejuruan_id') == $kejuruan->id ? 'selected' : '' }}>
                                {{ $kejuruan->kejuruan }}
                            </option>
                        @endforeach
                    </select>
                    @error('kejuruan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="bidang_pelatihan_id" class="block text-sm font-medium text-gray-700 mb-1">Bidang Pelatihan <span class="text-red-500">*</span></label>
                    <select name="bidang_pelatihan_id" id="bidang_pelatihan_id" required 
                            class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach(App\Models\BidangPelatihan::all() as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_pelatihan_id') == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->bidang_pelatihan }}
                            </option>
                        @endforeach
                    </select>
                    @error('bidang_pelatihan_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_hours" id="duration_hours" value="{{ old('duration_hours') }}" required min="1" 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('duration_hours') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="versi" class="block text-sm font-medium text-gray-700 mb-1">Versi</label>
                    <input type="number" name="versi" id="versi" value="{{ old('versi', 1) }}" min="1" 
                           class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Efektif</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" 
                        class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="file_program" class="block text-sm font-medium text-gray-700 mb-1">File Program (PDF/Doc)</label>
                    <input type="file" name="file_program" id="file_program" accept=".pdf,.doc,.docx" 
                        class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3" 
                          class="px-3 py-2 block w-full border rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Program Aktif</label>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <button type="button" @click="showForm = false" 
                        class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">Batal</button>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Simpan Master Program</button>
            </div>
        </form>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" class="flex items-center space-x-4">
            <input type="text" name="search" placeholder="Cari program..." value="{{ request('search') }}" 
                   class="flex-1 px-4 py-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
            <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">Cari</button>
        </form>
    </div>

    <!-- Master Programs List -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 table-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kejuruan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bidang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durasi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($masterPrograms as $mp)
                <tr class="hover:bg-gray-50 {{ is_null($mp->kejuruan_id) || is_null($mp->bidang_pelatihan_id) ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 text-sm font-medium truncate max-w-[200px]">{{ $mp->code }}</td>
                    <td class="px-6 py-4 text-sm font-medium">{{ $mp->name }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($mp->kejuruan_id)
                            {{ $mp->kejuruan->kejuruan ?? '-' }}
                        @else
                            <span class="text-yellow-600 text-xs font-medium">⚠ Belum diisi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($mp->bidang_pelatihan_id)
                            {{ $mp->bidangPelatihan->bidang_pelatihan ?? '-' }}
                        @else
                            <span class="text-yellow-600 text-xs font-medium">⚠ Belum diisi</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $mp->duration_hours }} jam</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $mp->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $mp->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.programs.master.show', $mp) }}" class="text-blue-600 hover:text-blue-800" title="Detail">
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
                            <form action="{{ route('admin.programs.master.destroy', $mp) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus master program ini?')">
                                @csrf @method('DELETE')
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
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada master program</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($masterPrograms->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $masterPrograms->links() }}
        </div>
        @endif
    </div>
</div>

<script>
let pollInterval = null;

// ── Tampilkan state banner ───────────────────────────────────
function showBannerState(state) {
    document.getElementById('sync-status-banner').classList.remove('hidden');
    ['sync-loading', 'sync-done', 'sync-error'].forEach(id => {
        document.getElementById(id).classList.add('hidden');
    });
    document.getElementById('sync-' + state).classList.remove('hidden');
}

// ── Parse progress NYATA dari isi log file ───────────────────
// Log punya baris seperti: " 87/105 [========================>---]  82%"
function parseProgressFromLog(logText) {
    if (!logText) return null;

    // Ambil semua kemunculan "current/total", pakai yang terakhir
    const matches = [...logText.matchAll(/(\d+)\/(\d+)\s+\[/g)];
    if (matches.length === 0) return null;

    const last    = matches[matches.length - 1];
    const current = parseInt(last[1]);
    const total   = parseInt(last[2]);
    if (!total) return null;

    return { current, total, pct: Math.round((current / total) * 100) };
}

function setProgressBar(pct, label) {
    const bar    = document.getElementById('sync-progress-bar');
    const lbl    = document.getElementById('sync-progress-label');
    const pctLbl = document.getElementById('sync-progress-pct');
    const clamped = Math.min(99, Math.max(0, Math.round(pct))); // max 99 selama masih running
    if (bar)    bar.style.width = clamped + '%';
    if (lbl && label) lbl.textContent = label;
    if (pctLbl) pctLbl.textContent   = clamped + '%';
}

function getLabel(current, total) {
    if (!current) return 'Menghubungkan ke API Kemnaker...';
    return `Memperbarui program ${current} dari ${total}...`;
}

// ── Parse ringkasan hasil di akhir log ───────────────────────
function parseSummaryFromLog(logText) {
    return {
        updated:  (logText.match(/Program diupdate[^\d]*(\d+)/)  || [])[1] ?? null,
        newProg:  (logText.match(/Program baru[^\d]*(\d+)/)       || [])[1] ?? null,
        notFound: (logText.match(/(\d+) program tidak ditemukan/) || [])[1] ?? null,
    };
}

function renderStats(logText) {
    const { updated, newProg, notFound } = parseSummaryFromLog(logText);
    const stats = document.getElementById('sync-stats');
    if (!stats) return;
    let html = '';
    if (newProg  !== null) html += `<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">+${newProg} program baru</span>`;
    if (updated  !== null) html += `<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-medium">${updated} diperbarui</span>`;
    if (notFound !== null && parseInt(notFound) > 0)
        html += `<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-medium">${notFound} tidak ditemukan</span>`;
    if (!html) html = '<span class="text-xs text-green-600">Data sudah lengkap, tidak ada perubahan</span>';
    stats.innerHTML = html;
}

// ── Polling setiap 2 detik ───────────────────────────────────
function pollSyncStatus() {
    fetch('{{ route("admin.programs.sync-status") }}')
        .then(r => r.json())
        .then(data => {
            if (data.running) {
                // Masih berjalan — update bar dari log nyata
                const progress = parseProgressFromLog(data.log);
                if (progress) {
                    setProgressBar(progress.pct, getLabel(progress.current, progress.total));
                }
                disableSyncButtons(true);
            } else {
                // Proses benar-benar selesai
                clearInterval(pollInterval);
                pollInterval = null;

                // Set bar ke 100% baru setelah proses selesai
                const bar    = document.getElementById('sync-progress-bar');
                const pctLbl = document.getElementById('sync-progress-pct');
                const lbl    = document.getElementById('sync-progress-label');
                if (bar)    bar.style.width    = '100%';
                if (pctLbl) pctLbl.textContent = '100%';
                if (lbl)    lbl.textContent    = 'Selesai!';

                setTimeout(() => {
                    showBannerState('done');
                    if (data.log) renderStats(data.log);
                    disableSyncButtons(false);
                }, 400);
            }
        })
        .catch(() => {
            clearInterval(pollInterval);
            pollInterval = null;
            showBannerState('error');
            disableSyncButtons(false);
        });
}

// ── Trigger sync via AJAX ────────────────────────────────────
function triggerSync(mode) {
    if (pollInterval) return;

    showBannerState('loading');
    setProgressBar(0, 'Menghubungkan ke API Kemnaker...');
    disableSyncButtons(true);

    fetch('{{ route("admin.programs.sync-kemnaker") }}?mode=' + mode, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(() => {
        // Mulai polling setelah 2 detik (beri waktu proses start)
        setTimeout(() => {
            pollInterval = setInterval(pollSyncStatus, 2000);
        }, 2000);
    })
    .catch(() => {
        showBannerState('error');
        disableSyncButtons(false);
    });
}

function disableSyncButtons(disabled) {
    ['btn-update-null', 'btn-full-sync'].forEach(id => {
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.classList.toggle('opacity-50', disabled);
        btn.classList.toggle('pointer-events-none', disabled);
    });
}

function closeSyncBanner() {
    document.getElementById('sync-status-banner').classList.add('hidden');
    clearInterval(pollInterval);
    pollInterval = null;
}

// Cek saat halaman load — kalau ada sync berjalan dari sebelumnya
document.addEventListener('DOMContentLoaded', () => {
    fetch('{{ route("admin.programs.sync-status") }}')
        .then(r => r.json())
        .then(data => {
            if (data.running) {
                showBannerState('loading');
                const progress = parseProgressFromLog(data.log);
                if (progress) {
                    setProgressBar(progress.pct, getLabel(progress.current, progress.total));
                } else {
                    setProgressBar(0, 'Mengambil data program...');
                }
                pollInterval = setInterval(pollSyncStatus, 2000);
                disableSyncButtons(true);
            }
        })
        .catch(() => {});
});
</script>
@endsection
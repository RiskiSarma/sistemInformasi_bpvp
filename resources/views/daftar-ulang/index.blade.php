@extends('layouts.app')

@section('title', 'Verifikasi Dokumen Daftar Ulang')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Verifikasi Dokumen Daftar Ulang</h1>
        <p class="text-sm text-gray-500 mt-1">Periksa dan verifikasi berkas yang diupload peserta</p>
    </div>

    {{-- Stats --}}
    @php
        $totalAll      = \App\Models\ParticipantDocument::count();
        $totalPending  = \App\Models\ParticipantDocument::where('status', 'pending')->count();
        $totalApproved = \App\Models\ParticipantDocument::where('status', 'approved')->count();
        $totalRejected = \App\Models\ParticipantDocument::where('status', 'rejected')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalAll }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Dokumen</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-2xl font-bold text-yellow-500">{{ $totalPending }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu Verifikasi</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-2xl font-bold text-green-500">{{ $totalApproved }}</p>
            <p class="text-xs text-gray-500 mt-1">Disetujui</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border text-center">
            <p class="text-2xl font-bold text-red-500">{{ $totalRejected }}</p>
            <p class="text-xs text-gray-500 mt-1">Ditolak</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <form method="GET" action="{{ route('admin.daftar-ulang.index') }}"
              class="flex flex-wrap gap-3 items-end">

            {{-- Cari Peserta --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Peserta</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama / email peserta..."
                       class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none w-52">
            </div>

            {{-- Filter Program --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Program Pelatihan</label>
                <select name="program_id"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none w-56">
                    <option value="">Semua Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}"
                            {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->nama_program ?? $program->name ?? $program->judul ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status"
                        class="border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                Filter
            </button>
            <a href="{{ route('admin.daftar-ulang.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition">
                Reset
            </a>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Program Pelatihan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Dokumen</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">File</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Diupload</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($documents as $doc)

                    {{-- ===================== ROW dengan Alpine Modal ===================== --}}
                    <tr class="hover:bg-gray-50 transition"
                        x-data="{
                            approveOpen: false,
                            rejectOpen: false,
                            docName: '{{ addslashes($doc->document_label) }}',
                            userName: '{{ addslashes($doc->user->name ?? '-') }}'
                        }">

                        {{-- Peserta --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">
                                        {{ substr($doc->user->name ?? '?', 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $doc->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $doc->user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Program --}}
                        <td class="px-5 py-4">
                            @if($doc->program)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                                    <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $doc->program->nama_program ?? $doc->program->name ?? $doc->program->judul ?? '-' }}
                                </span>
                            @else
                                @php
                                    $programNama = '-';
                                    if ($doc->user && method_exists($doc->user, 'participant')) {
                                        $participant = $doc->user->participant;
                                        if ($participant) {
                                            $p = $participant->program ?? null;
                                            if ($p) {
                                                $programNama = $p->nama_program ?? $p->name ?? $p->judul ?? '-';
                                            }
                                        }
                                    }
                                @endphp
                                @if($programNama !== '-')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                                        {{ $programNama }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum ada program</span>
                                @endif
                            @endif
                        </td>

                        {{-- Jenis Dokumen --}}
                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-gray-700">{{ $doc->document_label }}</p>
                            <p class="text-xs text-gray-400 mt-0.5 uppercase tracking-wide">{{ $doc->document_type }}</p>
                        </td>

                        {{-- File --}}
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.daftar-ulang.preview', $doc->id) }}"
                               target="_blank"
                               class="inline-flex items-center space-x-1.5 text-blue-600 hover:text-blue-800 text-sm">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span class="truncate max-w-[130px]">{{ $doc->file_name }}</span>
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $doc->file_size }}</p>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                            {{ $doc->created_at->format('d M Y') }}<br>
                            <span class="text-xs text-gray-400">{{ $doc->created_at->format('H:i') }}</span>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $doc->status_badge }}">
                                {{ $doc->status_label }}
                            </span>
                            @if($doc->catatan)
                                <p class="text-xs text-red-500 mt-1 max-w-[160px]">{{ $doc->catatan }}</p>
                            @endif
                            @if($doc->verified_at && $doc->status === 'approved')
                                <p class="text-xs text-gray-400 mt-0.5">{{ $doc->verified_at->format('d M Y') }}</p>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-4">
                            @if($doc->status !== 'approved')
                            <div class="flex flex-col gap-2">
                                {{-- Tombol Setujui → buka modal approve --}}
                                <button @click="approveOpen = true"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui
                                </button>

                                @if($doc->status !== 'rejected')
                                {{-- Tombol Tolak → buka modal reject --}}
                                <button @click="rejectOpen = true"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-red-50 text-red-600 text-xs font-medium rounded-lg transition border border-red-200 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak
                                </button>
                                @endif
                            </div>
                            @else
                                <div class="flex items-center space-x-1 text-green-600 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Sudah disetujui</span>
                                </div>
                            @endif
                        </td>

                        {{-- =================== MODAL APPROVE =================== --}}
                        <template x-teleport="body">
                            <div x-show="approveOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                 style="display: none;">

                                {{-- Backdrop --}}
                                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                                     @click="approveOpen = false"></div>

                                {{-- Panel --}}
                                <div x-show="approveOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                     class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">

                                    {{-- Icon --}}
                                    <div class="flex justify-center mb-4">
                                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Title --}}
                                    <h3 class="text-lg font-bold text-gray-800 text-center">Setujui Dokumen?</h3>
                                    <p class="text-sm text-gray-500 text-center mt-1 mb-1">
                                        Anda akan menyetujui dokumen
                                    </p>
                                    <p class="text-sm font-semibold text-blue-700 text-center bg-blue-50 rounded-lg py-2 px-4 mb-5">
                                        <span x-text="docName"></span>
                                        &mdash;
                                        <span x-text="userName"></span>
                                    </p>

                                    <p class="text-xs text-gray-400 text-center mb-6">
                                        Tindakan ini akan menandai dokumen sebagai <strong>Disetujui</strong> dan tidak dapat dibatalkan secara otomatis.
                                    </p>

                                    {{-- Actions --}}
                                    <div class="flex gap-3">
                                        <button @click="approveOpen = false"
                                                type="button"
                                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                                            Batal
                                        </button>

                                        <form action="{{ route('admin.daftar-ulang.approve', $doc->id) }}"
                                              method="POST"
                                              class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                                ✓ Ya, Setujui
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- =================== MODAL REJECT =================== --}}
                        <template x-teleport="body">
                            <div x-show="rejectOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                 style="display: none;">

                                {{-- Backdrop --}}
                                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                                     @click="rejectOpen = false"></div>

                                {{-- Panel --}}
                                <div x-show="rejectOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                     class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">

                                    {{-- Close button --}}
                                    <button @click="rejectOpen = false"
                                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>

                                    {{-- Icon --}}
                                    <div class="flex justify-center mb-4">
                                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Title --}}
                                    <h3 class="text-lg font-bold text-gray-800 text-center">Tolak Dokumen</h3>
                                    <p class="text-sm text-gray-500 text-center mt-1 mb-1">
                                        Anda akan menolak dokumen
                                    </p>
                                    <p class="text-sm font-semibold text-red-700 text-center bg-red-50 rounded-lg py-2 px-4 mb-5">
                                        <span x-text="docName"></span>
                                        &mdash;
                                        <span x-text="userName"></span>
                                    </p>

                                    {{-- Form --}}
                                    <form action="{{ route('admin.daftar-ulang.reject', $doc->id) }}" method="POST">
                                        @csrf

                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            Alasan Penolakan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="catatan"
                                                  rows="3"
                                                  required
                                                  placeholder="Tuliskan alasan penolakan dokumen ini secara jelas..."
                                                  class="w-full text-sm border border-gray-300 rounded-xl p-3 resize-none focus:ring-2 focus:ring-red-400 focus:border-red-400 focus:outline-none transition"></textarea>
                                        <p class="text-xs text-gray-400 mt-1 mb-5">
                                            Alasan ini akan dikirimkan kepada peserta sebagai notifikasi.
                                        </p>

                                        <div class="flex gap-3">
                                            <button type="button"
                                                    @click="rejectOpen = false"
                                                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                                                Tolak Dokumen
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </template>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-400">Tidak ada dokumen ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($documents->hasPages())
        <div class="px-5 py-4 border-t">
            {{ $documents->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
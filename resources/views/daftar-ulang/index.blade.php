@extends('layouts.app') {{-- sesuaikan dengan layout admin Anda --}}

@section('title', 'Verifikasi Dokumen Daftar Ulang')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Verifikasi Dokumen Daftar Ulang</h1>
        <div class="flex space-x-2">
            <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}"
               class="px-3 py-1.5 text-sm rounded-lg {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}"
               class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Pending
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}"
               class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Disetujui
            </a>
            <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}"
               class="px-3 py-1.5 text-sm rounded-lg {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Ditolak
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">File</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                <tr x-data="{ rejectOpen: false }">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800">{{ $doc->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $doc->user->email }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $doc->document_label }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.daftar-ulang.preview', $doc->id) }}"
                           target="_blank"
                           class="text-blue-600 hover:underline text-sm flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>{{ $doc->file_name }}</span>
                        </a>
                        <p class="text-xs text-gray-400">{{ $doc->file_size }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $doc->status_badge }}">
                            {{ $doc->status_label }}
                        </span>
                        @if($doc->catatan)
                            <p class="text-xs text-red-500 mt-1">{{ $doc->catatan }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($doc->status !== 'approved')
                        <div class="flex items-center space-x-2">
                            {{-- Approve --}}
                            <form action="{{ route('admin.daftar-ulang.approve', $doc->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg">
                                    Setujui
                                </button>
                            </form>

                            {{-- Reject --}}
                            @if($doc->status !== 'rejected')
                            <button @click="rejectOpen = true"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs rounded-lg">
                                Tolak
                            </button>
                            @endif
                        </div>

                        {{-- Form Reject --}}
                        <div x-show="rejectOpen" x-transition class="mt-2">
                            <form action="{{ route('admin.daftar-ulang.reject', $doc->id) }}" method="POST">
                                @csrf
                                <textarea name="catatan" rows="2" required
                                          placeholder="Alasan penolakan..."
                                          class="w-full text-xs border rounded-lg p-2 resize-none focus:ring-1 focus:ring-red-400"></textarea>
                                <div class="flex space-x-1 mt-1">
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg">
                                        Kirim
                                    </button>
                                    <button type="button" @click="rejectOpen = false"
                                            class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded-lg">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">Tidak ada dokumen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $documents->links() }}</div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Pengajar Eksternal')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengajar Eksternal</h2>
            <p class="text-gray-600 mt-1">Kelola data pengajar eksternal dan narasumber</p>
        </div>
        <a href="{{ route('admin.pengajar-eksternal.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Pengajar</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <form method="GET" id="filterForm" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="statusFilter" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pengajar</label>
                <div class="relative">
                    <input type="text" name="search" id="searchInput" 
                           value="{{ request('search') }}" 
                           placeholder="Nama, NIP, email, instansi..." 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 pr-10">
                    <div id="searchLoader" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Grid Card Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($pengajars as $pengajar)
        <div class="bg-white rounded-2xl shadow-sm border p-6 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-5">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-3xl font-bold text-blue-600">
                    {{ substr($pengajar->nama ?? 'P', 0, 1) }}
                </div>
                <span class="px-3 py-1 text-xs font-medium rounded-full 
                    {{ $pengajar->status === 'active' || !$pengajar->status ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $pengajar->status === 'active' || !$pengajar->status ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>

            <h3 class="font-semibold text-lg text-gray-800 mb-1">{{ $pengajar->nama }}</h3>
            <p class="text-sm text-gray-600 mb-2">{{ $pengajar->instansi ?? '-' }}</p>
            <p class="text-xs text-gray-500 mb-1">{{ $pengajar->email ?? '-' }}</p>
            <p class="text-xs text-gray-500">{{ $pengajar->telepon ?? '-' }}</p>

            @if($pengajar->jabatan)
                <p class="text-xs text-gray-600 mt-3">Jabatan: {{ $pengajar->jabatan }}</p>
            @endif

            <div class="grid grid-cols-2 gap-3 mt-6 pt-4 border-t">
                <!-- Tombol Detail tetap membuka modal lama Anda -->
                <button onclick="showDetailModal('{{ $pengajar->id }}')" 
                        class="px-4 py-3 border border-gray-300 rounded-2xl hover:bg-gray-50 transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail
                </button>

                <a href="{{ route('admin.pengajar-eksternal.schedule', $pengajar) }}" 
                   class="px-4 py-3 border border-purple-300 text-purple-700 rounded-2xl hover:bg-purple-50 transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Jadwal
                </a>

                <a href="{{ route('admin.pengajar-eksternal.attendance', $pengajar) }}" 
                   class="px-4 py-3 border border-green-300 text-green-700 rounded-2xl hover:bg-green-50 transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Absensi
                </a>

                <a href="{{ route('admin.pengajar-eksternal.edit', $pengajar) }}" 
                   class="px-4 py-3 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-dashed">
            <p class="text-gray-500">Belum ada data pengajar eksternal</p>
        </div>
        @endforelse
    </div>

    @if($pengajars->hasPages())
    <div class="flex justify-center mt-8">
        {{ $pengajars->links() }}
    </div>
    @endif
</div>

<script>
function showDetailModal(id) {
    // Ini akan membuka halaman show yang lama (modal dengan tab Info Dasar, Program, Sub Units)
    window.location.href = `/admin/pengajar-eksternal/${id}`;
}
</script>
@endsection
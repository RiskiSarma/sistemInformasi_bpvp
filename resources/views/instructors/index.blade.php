@extends('layouts.app')

@section('title', 'Data Instruktur')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Instruktur</h2>
            <p class="text-gray-600 mt-1">Kelola data instruktur pelatihan</p>
        </div>
        <a href="{{ route('admin.instructors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Instruktur</span>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Instruktur</label>
                <div class="relative">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Nama, email, keahlian..." class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 pr-10">
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

    <!-- Instructors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($instructors as $instructor)
        <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-4">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-purple-600">{{ substr($instructor->name, 0, 1) }}</span>
                </div>
                <span class="px-2 py-1 text-xs rounded-full {{ $instructor->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($instructor->status) }}
                </span>
            </div>
            
            <h3 class="font-semibold text-gray-800 text-lg mb-1">{{ $instructor->name }}</h3>
            <p class="text-sm text-gray-600 mb-2">{{ $instructor->expertise }}</p>
            <p class="text-xs text-gray-500 mb-1">{{ $instructor->email }}</p>
            <p class="text-xs text-gray-500 mb-4">{{ $instructor->phone }}</p>
            
            @if($instructor->experience_years)
            <p class="text-xs text-gray-600 mb-4">Pengalaman: {{ $instructor->experience_years }} tahun</p>
            @endif

            <div class="flex items-center space-x-2 pt-4 border-t">
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="flex-1 px-3 py-2 text-center border rounded-lg hover:bg-gray-50 transition text-sm">
                    Profil
                </a>
                <a href="{{ route('admin.instructors.schedule', $instructor) }}" class="flex-1 px-3 py-2 text-center border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition text-sm">
                    Jadwal
                </a>
                <a href="{{ route('admin.instructors.edit', $instructor) }}" class="flex-1 px-3 py-2 text-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    Edit
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-500">Belum ada data instruktur</p>
            @if(request('search'))
            <p class="text-sm text-gray-400 mt-2">Tidak ada hasil untuk "{{ request('search') }}"</p>
            <a href="{{ route('admin.instructors.index') }}" class="mt-3 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
                Reset Pencarian →
            </a>
            @endif
        </div>
        @endforelse
    </div>

    @if($instructors->hasPages())
    <div class="flex justify-center">
        {{ $instructors->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const filterForm = document.getElementById('filterForm');
    const searchLoader = document.getElementById('searchLoader');
    let searchTimeout;

    // Real-time search
    searchInput.addEventListener('input', function() {
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Show loader
        searchLoader.classList.remove('hidden');
        
        // Set new timeout (debounce 500ms)
        searchTimeout = setTimeout(function() {
            filterForm.submit();
        }, 500);
    });

    // Real-time status filter
    statusFilter.addEventListener('change', function() {
        searchLoader.classList.remove('hidden');
        filterForm.submit();
    });

    // Hide loader on page load
    searchLoader.classList.add('hidden');
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen User</h2>
            <p class="text-gray-600 mt-1">Kelola akun login sistem</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Tambah User Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Form Search & Filter Realtime -->
            <div class="p-4 border-b bg-gray-50 flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[300px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama / Email</label>
                    <input 
                        type="text" 
                        id="search" 
                        placeholder="Ketik untuk mencari..." 
                        value="{{ request('search') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <div class="w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Role</label>
                    <select id="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instruktur</option>
                        <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Peserta</option>
                    </select>
                </div>

                <div>
                    <button type="button" id="reset" class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                        Reset
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Profil</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat Oleh</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diupdate Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs rounded-full {{ 
                                    $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 
                                    ($user->role === 'instructor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')
                                }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->isInstructor() && $user->instructor)
                                    <a href="{{ route('admin.instructors.edit', $user->instructor) }}" class="text-blue-600 hover:underline">Edit Profil Instruktur</a>
                                @elseif($user->isParticipant() && $user->participant)
                                    <a href="{{ route('admin.participants.edit', $user->participant) }}" class="text-blue-600 hover:underline">Edit Profil Peserta</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus user ini?')" class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->creator?->name ?? 'Sistem' }}
                                <br>
                                <span class="text-xs text-gray-500">
                                    {{ $user->created_at->format('d M Y H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->updater?->name ?? '-' }}
                                @if($user->updater)
                                <br>
                                <span class="text-xs text-gray-500">
                                    {{ $user->updated_at->format('d M Y H:i') }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                Belum ada user terdaftar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Realtime Search & Filter -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const roleSelect = document.getElementById('role');
    const resetBtn = document.getElementById('reset');

    let timeout;

    function applyFilters() {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
        if (roleSelect.value) params.append('role', roleSelect.value);

        window.location.search = params.toString();
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        timeout = setTimeout(applyFilters, 600);
    });

    roleSelect.addEventListener('change', applyFilters);

    resetBtn.addEventListener('click', function () {
        window.location.href = '{{ route('admin.users.index') }}';
    });
});
</script>
@endsection
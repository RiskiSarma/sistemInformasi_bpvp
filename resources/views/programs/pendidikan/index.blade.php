@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Master Data Pendidikan</h1>
            <p class="mt-1 text-sm text-gray-600">Kelola jenjang pendidikan yang digunakan dalam sistem</p>
        </div>
        <button type="button" 
                data-modal-target="createModal"
                data-modal-toggle="createModal"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg shadow hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Pendidikan
        </button>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Card Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Jenjang Pendidikan</h3>
                
                <form method="GET" action="{{ route('admin.pendidikan.index') }}" class="flex items-center gap-2">
                    <input type="text" name="search" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64" placeholder="Cari jenjang pendidikan..." value="{{ request('search') }}">
                    <button type="submit" class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-search text-gray-600"></i>
                    </button>
                    @if(request('search'))
                    <a href="{{ route('admin.pendidikan.index') }}" class="p-2 bg-red-50 rounded-lg hover:bg-red-100 transition text-red-600">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenjang Pendidikan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Digunakan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendidikans as $i => $pendidikan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $pendidikans->firstItem() + $i }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $pendidikan->pendidikan }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $totalUsed = $pendidikan->participants()->count() + $pendidikan->instructors()->count();
                            @endphp
                            @if($totalUsed > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $totalUsed }} data
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Belum digunakan
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium flex justify-center gap-2">
                            <button type="button" 
                                    data-modal-target="viewModal{{ $pendidikan->id }}"
                                    data-modal-toggle="viewModal{{ $pendidikan->id }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-md hover:bg-indigo-200 transition">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            <button type="button" 
                                    data-modal-target="editModal{{ $pendidikan->id }}"
                                    data-modal-toggle="editModal{{ $pendidikan->id }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-md hover:bg-yellow-200 transition">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            <button type="button" 
                                    data-modal-target="deleteModal{{ $pendidikan->id }}"
                                    data-modal-toggle="deleteModal{{ $pendidikan->id }}"
                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 rounded-md hover:bg-red-200 transition">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                            <i class="fas fa-inbox fa-4x mb-4 text-gray-300 block"></i>
                            <p class="text-lg">Tidak ada data pendidikan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendidikans->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $pendidikans->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Create (Tambah) -->
<div id="createModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="relative w-full max-w-md p-4">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Tambah Pendidikan Baru</h3>
                <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="createModal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.pendidikan.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-900">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                    <input type="text" name="pendidikan" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('pendidikan') }}" placeholder="Contoh: D4/S1">
                    @error('pendidikan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Contoh: SD/Sederajat, SMP/Sederajat, SMA/SMK, D3, S1, S2, S3</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" data-modal-hide="createModal">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit, Delete, View -->
@foreach($pendidikans as $pendidikan)
    <!-- View Modal (bagian dalam) -->
<div id="viewModal{{ $pendidikan->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="relative w-full max-w-3xl p-4">
        <div class="relative bg-white rounded-lg shadow">
            <div class="flex items-center justify-between p-4 border-b bg-indigo-50">
                <h3 class="text-xl font-semibold text-gray-900">Data yang Menggunakan "{{ $pendidikan->pendidikan }}"</h3>
                <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="viewModal{{ $pendidikan->id }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                @php
                    $pesertas = $pendidikan->participants;
                    $instrukturs = $pendidikan->instructors;
                @endphp

                @if($pesertas->isEmpty() && $instrukturs->isEmpty())
                    <p class="text-center text-gray-600 py-8">Tidak ada data yang menggunakan jenjang ini.</p>
                @else
                    @if($pesertas->isNotEmpty())
                        <div class="mb-8">
                            <h4 class="font-bold text-gray-800 mb-3">Peserta ({{ $pesertas->count() }} orang)</h4>
                            <div class="border border-gray-200 rounded overflow-hidden">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3">NIK</th>
                                            <th class="px-4 py-3">Nama</th>
                                            <th class="px-4 py-3">Program</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pesertas as $peserta)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-4 py-3">{{ $peserta->nik ?? '-' }}</td>
                                                <!-- Ambil nama dari relasi users -->
                                                <td class="px-4 py-3">{{ $peserta->user->name ?? $peserta->user->full_name ?? $peserta->user->nama_lengkap ?? 'Tidak ada nama' }}</td>
                                                <!-- Ambil nama program dari relasi program -->
                                                <td class="px-4 py-3">{{ $peserta->program->nama ?? $peserta->program->name ?? 'Tidak ada program' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if($instrukturs->isNotEmpty())
                        <div>
                            <h4 class="font-bold text-gray-800 mb-3">Pengajar ({{ $instrukturs->count() }} orang)</h4>
                            <div class="border border-gray-200 rounded overflow-hidden">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3">Nama</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Expertise</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($instrukturs as $instruktur)
                                            <tr class="border-b hover:bg-gray-50">
                                                <td class="px-4 py-3">{{ $instruktur->name }}</td>
                                                <td class="px-4 py-3">{{ $instruktur->email }}</td>
                                                <td class="px-4 py-3">{{ $instruktur->expertise ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <div class="flex justify-end p-4 border-t">
                <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300" data-modal-hide="viewModal{{ $pendidikan->id }}">Tutup</button>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Modal -->
    <div id="editModal{{ $pendidikan->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="relative w-full max-w-md p-4">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b bg-yellow-50">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Pendidikan</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="editModal{{ $pendidikan->id }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.pendidikan.update', $pendidikan) }}" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Jenjang Pendidikan <span class="text-red-500">*</span></label>
                        <input type="text" name="pendidikan" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-yellow-500 focus:border-yellow-500" value="{{ old('pendidikan', $pendidikan->pendidikan) }}">
                        @error('pendidikan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Contoh: SD/Sederajat, SMP/Sederajat, SMA/SMK, D3, S1, S2, S3</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" data-modal-hide="editModal{{ $pendidikan->id }}">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal{{ $pendidikan->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="relative w-full max-w-md p-4">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b bg-red-50">
                    <h3 class="text-xl font-semibold text-red-900">Hapus Pendidikan</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="deleteModal{{ $pendidikan->id }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.pendidikan.destroy', $pendidikan) }}" class="p-6 space-y-4">
                    @csrf @method('DELETE')
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <p class="text-sm text-yellow-700">Apakah Anda yakin ingin menghapus jenjang pendidikan ini?</p>
                    </div>
                    <p class="text-sm"><strong>Jenjang:</strong> {{ $pendidikan->pendidikan }}</p>
                    @php $totalUsed = $pendidikan->participants()->count() + $pendidikan->instructors()->count(); @endphp
                    @if($totalUsed > 0)
                    <div class="bg-red-50 border-l-4 border-red-400 p-4">
                        <p class="text-sm text-red-700"><strong>Perhatian!</strong> Data ini sedang digunakan oleh {{ $totalUsed }} data lainnya.</p>
                    </div>
                    @endif
                    <div class="flex justify-end gap-3">
                        <button type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300" data-modal-hide="deleteModal{{ $pendidikan->id }}">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@push('scripts')
<script>
    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => el.classList.add('opacity-0', 'transition-opacity'));
    }, 5000);

    // Re-show modal on validation error
    @if($errors->any())
        @if(old('_method') === 'PUT')
            const editId = '{{ old('id') }}';
            if (editId) {
                const modalEl = document.getElementById(`editModal${editId}`);
                if (modalEl) {
                    modalEl.classList.remove('hidden');
                    const modal = new Flowbite.Modal(modalEl);
                    modal.show();
                }
            }
        @else
            const createModalEl = document.getElementById('createModal');
            if (createModalEl) {
                createModalEl.classList.remove('hidden');
                const modal = new Flowbite.Modal(createModalEl);
                modal.show();
            }
        @endif
    @endif
</script>
@endpush
@endsection
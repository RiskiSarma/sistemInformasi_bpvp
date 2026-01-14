@extends('layouts.app')

@section('title', 'Detail Master Program')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.programs.master') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
       
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.programs.master.edit', $masterProgram) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Edit Master Program
            </a>
        </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $masterProgram->name }}</h2>
                <p class="text-gray-600 mt-1">Kode: <span class="font-mono font-semibold">{{ $masterProgram->code }}</span></p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm {{ $masterProgram->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $masterProgram->is_active ? 'Aktif' : 'Tidak Aktif' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Kejuruan</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->kejuruan ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Bidang</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->bidang ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Jenis Pelatihan</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->jenis_pelatihan_full }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Durasi Pelatihan</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->duration_hours }} Jam</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-2">Total Unit Kompetensi</h3>
                <p class="text-lg font-semibold text-gray-800">{{ $masterProgram->independentCompetencyUnits->count() }} Unit</p>
            </div>
        </div>

        @if($masterProgram->description)
        <div class="mt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h3>
            <p class="text-gray-700 leading-relaxed">{{ $masterProgram->description }}</p>
        </div>
        @endif

        <div class="mt-6 pt-6 border-t">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $masterProgram->creator?->name ?? 'Sistem' }}
                        <span class="text-gray-500 text-xs">
                            ({{ $masterProgram->created_at->format('d M Y H:i') }})
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui Oleh</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $masterProgram->updater?->name ?? 'Sistem' }}
                        <span class="text-gray-500 text-xs">
                            ({{ $masterProgram->updated_at->format('d M Y H:i') }})
                        </span>
                    </dd>
                </div>
                <td>
                    @if($masterProgram->trashed())
                        <span class="text-red-600">Dihapus</span>
                    @else
                        <span class="text-green-600">Aktif</span>
                    @endif
                </td>
            </dl>
        </div>
    </div>

    <!-- Unit Kompetensi Independen -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Unit Kompetensi Independen</h3>
            <button onclick="document.getElementById('addUnitModal').classList.remove('hidden')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Unit
            </button>
        </div>

        @if($masterProgram->independent_competency_units->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($masterProgram->independent_competency_units as $index => $unit)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-mono text-sm">{{ $unit->code }}</td>
                        <td class="px-6 py-4 text-sm">{{ $unit->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($unit->description ?? '-', 60) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex space-x-4">
                                <button onclick="editUnit({{ $unit->id }}, '{{ addslashes($unit->code) }}', '{{ addslashes($unit->name) }}', '{{ addslashes($unit->description ?? '') }}')" class="text-green-600 hover:text-green-800 font-medium">
                                    Edit
                                </button>
                                <form action="{{ route('admin.programs.master.units.destroy', [$masterProgram, $unit]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin hapus unit ini?')" class="text-red-600 hover:text-red-800 font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center text-gray-500">
            Belum ada unit kompetensi independen
        </div>
        @endif
    </div>

    <!-- Modal Tambah Unit -->
    <div id="addUnitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Tambah Unit Kompetensi Independen</h3>
            <form action="{{ route('admin.programs.master.units.store', $masterProgram) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700">Kode Unit</label>
                    <input type="text" name="code" id="code" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Unit</label>
                    <input type="text" name="name" id="name" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('addUnitModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Unit -->
    <div id="editUnitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">Edit Unit Kompetensi</h3>
            <form id="editUnitForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="unit_id" id="editUnitId">
                <div class="mb-4">
                    <label for="editCode" class="block text-sm font-medium text-gray-700">Kode Unit</label>
                    <input type="text" name="code" id="editCode" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="editName" class="block text-sm font-medium text-gray-700">Nama Unit</label>
                    <input type="text" name="name" id="editName" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="mb-4">
                    <label for="editDescription" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" id="editDescription" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('editUnitModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript untuk Edit Modal -->
    <script>
    function editUnit(id, code, name, description) {
        document.getElementById('editUnitId').value = id;
        document.getElementById('editCode').value = code;
        document.getElementById('editName').value = name;
        document.getElementById('editDescription').value = description || '';
        document.getElementById('editUnitForm').action = '{{ route("admin.programs.master.units.update", [$masterProgram, ":id"]) }}'.replace(':id', id);
        document.getElementById('editUnitModal').classList.remove('hidden');
    }
    </script>

    <!-- Program yang Menggunakan -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="p-6 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Program yang Menggunakan</h3>
        </div>
        @if($masterProgram->programs->count() > 0)
        <div class="divide-y">
            @foreach($masterProgram->programs as $program)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $program->batch }}</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $program->start_date->format('d M Y') }} - {{ $program->end_date->format('d M Y') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full {{ $program->status === 'ongoing' ? 'bg-green-100 text-green-800' : ($program->status === 'planned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($program->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center text-gray-500">
            Belum ada program yang menggunakan master ini
        </div>
        @endif
    </div>
</div>
@endsection
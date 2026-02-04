@extends('layouts.app')

@section('title', 'Detail Master Program')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'info' }">
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

    <!-- Header Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-start justify-between mb-4">
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
                <h3 class="text-sm font-medium text-gray-500 mb-1">Kejuruan</h3>
                <p class="text-base font-semibold text-gray-800">{{ $masterProgram->kejuruan->kejuruan ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Bidang Pelatihan</h3>
                <p class="text-base font-semibold text-gray-800">{{ $masterProgram->bidangPelatihan->bidang_pelatihan ?? '-' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Durasi</h3>
                <p class="text-base font-semibold text-gray-800">{{ $masterProgram->duration_hours }} Jam</p>
            </div>
        </div>

        @if($masterProgram->description)
        <div class="mt-4 pt-4 border-t">
            <h3 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h3>
            <p class="text-gray-700">{{ $masterProgram->description }}</p>
        </div>
        @endif
    </div>
    
    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'info'" 
                        :class="activeTab === 'info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Info Dasar
                </button>
                <button @click="activeTab = 'units'" 
                        :class="activeTab === 'units' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Program Pelatihan Units
                </button>
                <button @click="activeTab = 'programs'" 
                        :class="activeTab === 'programs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition">
                    Daftar Program ({{ $masterProgram->programs->count() }})
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Tab 1: Info Dasar -->
            <div x-show="activeTab === 'info'" x-transition>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Efektif</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $masterProgram->tanggal ? $masterProgram->tanggal->format('d M Y') : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 mb-1">File Program</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($masterProgram->file_program)
                                    <a href="{{ route('admin.programs.master.preview-file', $masterProgram) }}" 
                                    target="_blank" 
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition mr-2">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Preview File
                                    </a>
                                    <a href="{{ Storage::url($masterProgram->file_program) }}" 
                                    download 
                                    class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download File
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Tidak ada file</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Dibuat Oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $masterProgram->creator?->name ?? 'Sistem' }}
                                <span class="text-gray-500 text-xs block">
                                    {{ $masterProgram->created_at->format('d M Y H:i') }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Terakhir Diperbarui</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $masterProgram->updater?->name ?? 'Sistem' }}
                                <span class="text-gray-500 text-xs block">
                                    {{ $masterProgram->updated_at->format('d M Y H:i') }}
                                </span>
                            </dd>
                        </div>
                    </div>

                    <div class="pt-4 border-t">
                        <form action="{{ route('admin.programs.master.destroy', $masterProgram) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus master program akan menghapus semua data terkait. Yakin ingin melanjutkan?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Hapus Master Program Ini
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Program Pelatihan Units (PIVOT TABLE) -->
            <div x-show="activeTab === 'units'" x-transition>
                <div class="space-y-4">
                    <!-- Form Tambah Unit -->
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <h4 class="font-medium text-gray-800 mb-3">Tambah Unit Kompetensi ke Master Program</h4>
                        <p class="text-sm text-gray-600 mb-4">Data akan masuk ke tabel pivot <code class="bg-gray-200 px-1 rounded font-mono text-xs">program_pelatihan_units</code></p>
                        
                        <form action="{{ route('admin.programs.master.units.store', $masterProgram) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kompetensi *</label>
                                    <select name="independent_competency_unit_id" required
                                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach(\App\Models\IndependentCompetencyUnit::orderBy('code')->get() as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->code }} - {{ Str::limit($unit->name, 50) }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">Pilih dari Independent Competency Units</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Unit *</label>
                                    <select name="type_unit" required
                                            class="w-full px-3 py-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                        <option value="skkni">SKKNI</option>
                                        <option value="non-skkni">Non-SKKNI</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">JP (Jam Pelajaran)</label>
                                    <input type="number" name="jp" min="0" value="0" placeholder="0"
                                           class="w-full px-3 py-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Unit
                                </span>
                            </button>
                        </form>
                    </div>

                    <!-- Daftar Units yang Sudah Ditambahkan -->
                    <div class="border rounded-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">JP</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKKNI</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @php
                                    $units = $masterProgram->independentCompetencyUnits()
                                                ->with('skkni')
                                                ->orderBy('code')
                                                ->get();
                                @endphp

                                @forelse($units as $unit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-mono">{{ $unit->code }}</td>
                                    <td class="px-4 py-3">{{ $unit->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $unit->pivot->type_unit === 'skkni' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ strtoupper($unit->pivot->type_unit ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $unit->pivot->jp ?? 0 }}</td>
                                    <td class="px-4 py-3">
                                        @if($unit->skkni)
                                            {{ $unit->skkni->nomor ?? '-' }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-4">
                                            <!-- Icon Edit -->
                                            <button 
                                                type="button"
                                                title="Edit Unit Kompetensi"
                                                @click="$dispatch('open-edit-unit-modal', { 
                                                    unitId: '{{ $unit->id }}',
                                                    typeUnit: '{{ $unit->pivot->type_unit ?? 'skkni' }}',
                                                    jp: '{{ $unit->pivot->jp ?? 0 }}'
                                                })"
                                                class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Icon Hapus -->
                                            <button 
                                                type="button"
                                                title="Hapus Unit Kompetensi"
                                                @click="$dispatch('open-delete-unit-modal', { 
                                                    unitId: '{{ $unit->id }}',
                                                    unitName: '{{ addslashes($unit->name) }}'
                                                })"
                                                class="text-red-600 hover:text-red-800 transition-colors duration-150"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-8 text-gray-500">Belum ada unit kompetensi</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($units->count() > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Total: {{ $units->count() }} unit kompetensi</p>
                                <p class="text-xs mt-1">Unit-unit ini terhubung dengan master program <strong>{{ $masterProgram->name }}</strong></p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tab 3: Daftar Program -->
            <div x-show="activeTab === 'programs'" x-transition>
                @if($masterProgram->programs->count() > 0)
                <div class="space-y-3">
                    @foreach($masterProgram->programs as $program)
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
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
                <div class="text-center py-12 text-gray-500">
                    Belum ada program yang menggunakan master ini
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- Modal Edit Unit -->
<div x-data="{ openEdit: false, form: { type_unit: '', jp: 0, unit_id: '' } }"
     x-show="openEdit"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition
     @open-edit-unit-modal.window="openEdit = true; form = { type_unit: $event.detail.typeUnit, jp: $event.detail.jp, unit_id: $event.detail.unitId }">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-40" @click="openEdit = false"></div>
        
        <div class="bg-white rounded-lg shadow-xl z-10 w-full max-w-md p-6 relative">
            <h3 class="text-lg font-medium mb-4">Edit Unit Kompetensi</h3>
            
            <form :action="`/admin/programs/master/{{ $masterProgram->id }}/units/${form.unit_id}`" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Unit</label>
                        <select x-model="form.type_unit" name="type_unit" class="mt-1 block w-full border-gray-300 rounded-md">
                            <option value="skkni">SKKNI</option>
                            <option value="non-skkni">Non-SKKNI</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">JP (Jam Pelajaran)</label>
                        <input type="number" x-model="form.jp" name="jp" class="mt-1 block w-full border-gray-300 rounded-md" min="0">
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div x-data="{ openDelete: false, unitId: '', unitName: '' }"
     x-show="openDelete"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition
     @open-delete-unit-modal.window="openDelete = true; unitId = $event.detail.unitId; unitName = $event.detail.unitName">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-40" @click="openDelete = false"></div>
        
        <div class="bg-white rounded-lg shadow-xl z-10 w-full max-w-md p-6">
            <h3 class="text-lg font-medium text-red-700 mb-4">Konfirmasi Hapus</h3>
            <p class="text-gray-700 mb-6">
                Yakin ingin menghapus unit <strong x-text="unitName"></strong> ?
            </p>

            <form :action="`/admin/programs/master/{{ $masterProgram->id }}/units/${unitId}`" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('DELETE')

                <button type="button" @click="openDelete = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
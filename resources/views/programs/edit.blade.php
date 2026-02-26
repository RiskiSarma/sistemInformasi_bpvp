@extends('layouts.app')

@section('title', 'Edit Program')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.programs.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Program Pelatihan</h2>

        <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div x-data="{
                selectedMaster: '{{ old('master_program_id', $program->master_program_id) }}',
                masterName: '{{ $program->masterProgram->name ?? '' }}',
                selectedPaket: '{{ old('paket_pelatihan_id', $program->paket_pelatihan_id) }}',
                jenisPelatihan: '{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '' }}',
                availableUnits: [],
                selectedUnits: {{ json_encode($program->selected_units_config ? collect($program->selected_units_config)->pluck('unit_id')->toArray() : []) }},
                unitDurations: {{ json_encode($program->selected_units_config ? collect($program->selected_units_config)->pluck('custom_duration', 'unit_id')->toArray() : []) }},
                unitTypes: {{ json_encode($program->selected_units_config ? collect($program->selected_units_config)->pluck('type', 'unit_id')->toArray() : []) }},
                adaIndustri: '{{ old('ada_industri', $program->ada_industri) }}',
                totalJP: {{ $program->total_jp_from_selected_units ?? 0 }},
                selectedInstructors: {{ json_encode($program->programInstructors->pluck('instructor_id')->toArray()) }},
                penanggungJawab: '{{ $program->programInstructors->where('is_penanggung_jawab', true)->first()->instructor_id ?? '' }}',
                angkatan: '{{ old('angkatan', $program->angkatan) }}',
                angkatanLoading: false,
                angkatanInfo: 'Angkatan saat ini',
                isAngkatanChanged: false,

                loadUnits(event) {
                    const option = event.target.options[event.target.selectedIndex];
                    const unitsData = option.getAttribute('data-units');
                    this.masterName = option.getAttribute('data-name') || '';
                    
                    if (unitsData) {
                        this.availableUnits = JSON.parse(unitsData);
                    }
                },

                recomputeAngkatan() {
                    if (!this.selectedMaster || !this.selectedPaket) return;
                    this.angkatanLoading = true;
                    this.isAngkatanChanged = true;
                    fetch(`/admin/programs/next-angkatan?master_program_id=${this.selectedMaster}&paket_pelatihan_id=${this.selectedPaket}&exclude_program_id={{ $program->id }}`)
                        .then(res => res.json())
                        .then(data => {
                            this.angkatan = data.angkatan;
                            this.angkatanInfo = data.info || '';
                            this.angkatanLoading = false;
                        })
                        .catch(() => {
                            this.angkatanLoading = false;
                        });
                },

                updatePaket(event) {
                    const option = event.target.options[event.target.selectedIndex];
                    this.jenisPelatihan = option.getAttribute('data-jenis') || '';
                    this.recomputeAngkatan();
                },

                toggleUnit(unitId, unit) {
                    const index = this.selectedUnits.indexOf(unitId);
                    if (index > -1) {
                        this.selectedUnits.splice(index, 1);
                        delete this.unitDurations[unitId];
                        delete this.unitTypes[unitId];
                    } else {
                        this.selectedUnits.push(unitId);
                        this.unitDurations[unitId] = unit.pivot?.jp || unit.jp || 0;
                        this.unitTypes[unitId] = unit.pivot?.type_unit || 'reguler';
                    }
                    this.calculateTotal();
                },

                updateDuration(unitId, value) {
                    this.unitDurations[unitId] = parseInt(value) || 0;
                    this.calculateTotal();
                },

                calculateTotal() {
                    this.totalJP = Object.values(this.unitDurations).reduce((sum, val) => sum + (parseInt(val) || 0), 0);
                },

                updateJenis(event) {
                    const option = event.target.options[event.target.selectedIndex];
                    this.jenisPelatihan = option.getAttribute('data-jenis') || '';
                },

                init() {
                    // Load units from selected master on page load
                    const masterSelect = document.getElementById('master_program_id');
                    if (this.selectedMaster && masterSelect) {
                        const option = masterSelect.querySelector(`option[value='${this.selectedMaster}']`);
                        if (option) {
                            const unitsData = option.getAttribute('data-units');
                            if (unitsData) {
                                this.availableUnits = JSON.parse(unitsData);
                            }
                        }
                    }

                    // Watch untuk auto-select PJ
                    this.$watch('selectedInstructors', (value) => {
                        if (value.length === 1) {
                            this.penanggungJawab = value[0];
                        }
                        if (!value.includes(this.penanggungJawab)) {
                            this.penanggungJawab = '';
                        }
                    });
                }
            }">

            <!-- Master Program & Paket -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="master_program_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Master Program <span class="text-red-500">*</span>
                    </label>
                    <select name="master_program_id" 
                            id="master_program_id" 
                            required 
                            x-model="selectedMaster"
                            @change="loadUnits($event)"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Master Program</option>
                        @foreach($masterPrograms as $mp)
                        <option value="{{ $mp->id }}" 
                                data-units='@json($mp->independentCompetencyUnits)'
                                data-name="{{ $mp->name }}">
                            {{ $mp->code }} - {{ $mp->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('master_program_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="paket_pelatihan_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Paket Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <select name="paket_pelatihan_id" 
                            id="paket_pelatihan_id" 
                            required
                            x-model="selectedPaket"
                            @change="updateJenis($event)"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Paket</option>
                        @foreach($paketPelatihans as $paket)
                        <option value="{{ $paket->id }}" 
                                data-jenis="{{ $paket->jenisPelatihan->jenis_pelatihan ?? 'Unknown' }}">
                            {{ $paket->jenisPelatihan->jenis_pelatihan ?? 'Unknown' }} - {{ $paket->tahun }} - Batch {{ $paket->batch }}
                        </option>
                        @endforeach
                    </select>
                    @error('paket_pelatihan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Info Jenis & Edit Angkatan -->
            <div class="bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-200 rounded-lg p-5"
                 x-show="selectedMaster && selectedPaket">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <div>
                        <span class="text-sm font-medium text-gray-700 mb-2 block">Jenis Pelatihan:</span>
                        <div class="px-4 py-2 inline-block text-sm rounded-full bg-purple-600 text-white font-medium"
                             x-text="jenisPelatihan || '-'"></div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-700 mb-3 block">
                            Angkatan <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 font-normal ml-1">(otomatis dihitung)</span>
                        </span>

                        <input type="hidden" name="angkatan" :value="angkatan">

                        <!-- Loading -->
                        <div x-show="angkatanLoading" class="flex items-center gap-2 text-blue-600 text-sm">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>Menghitung...</span>
                        </div>

                        <!-- Badge Angkatan -->
                        <div x-show="!angkatanLoading && angkatan" class="flex items-center gap-3">
                            <div class="px-5 py-2 bg-blue-600 text-white text-lg font-bold rounded-lg shadow flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Angkatan <span x-text="angkatan" class="ml-1"></span>
                            </div>
                            <span x-show="isAngkatanChanged" class="text-xs text-amber-600 bg-amber-50 border border-amber-200 px-2 py-1 rounded">
                                ⚠ Angkatan berubah
                            </span>
                        </div>

                        <p x-show="angkatanInfo && !angkatanLoading" class="text-xs text-gray-600 mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="angkatanInfo"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Unit Kompetensi -->
            <div x-show="availableUnits.length > 0">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Unit Kompetensi (dari Master Program)</h3>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm text-yellow-800">
                    Unit kompetensi diambil dari <strong>Master Program</strong>. Edit <strong>durasi custom</strong> sesuai kebutuhan.
                </div>

                <div class="space-y-3">
                    <template x-for="unit in availableUnits" :key="unit.id">
                        <div class="border rounded-lg p-4"
                             :class="selectedUnits.includes(unit.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                            <div class="flex items-start gap-4">
                                <!-- Checkbox & Info -->
                                <div class="flex items-start flex-1">
                                    <input type="checkbox" 
                                           :id="'unit-' + unit.id"
                                           :value="unit.id"
                                           :checked="selectedUnits.includes(unit.id)"
                                           @change="toggleUnit(unit.id, unit)"
                                           class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">
                                    
                                    <label :for="'unit-' + unit.id" class="ml-3 flex-1 cursor-pointer">
                                        <div class="font-mono text-sm font-bold" x-text="unit.code"></div>
                                        <div class="text-sm text-gray-700 mt-1" x-text="unit.name"></div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            JP Default: <span x-text="unit.pivot?.jp || unit.jp || 0"></span> jam
                                        </div>
                                    </label>
                                </div>

                                <!-- Input Durasi & Tipe -->
                                <template x-if="selectedUnits.includes(unit.id)">
                                    <div class="flex items-center gap-3 bg-white p-3 rounded border">
                                        <input type="hidden" :name="'selected_units[]'" :value="unit.id">
                                        
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                                            <select :name="'unit_types[' + unit.id + ']'"
                                                    x-model="unitTypes[unit.id]"
                                                    class="px-2 py-1.5 text-sm border rounded w-28">
                                                <option value="reguler">Reguler</option>
                                                <option value="softskill">Softskill</option>
                                                <option value="skkni">SKKNI</option>
                                                <option value="industri" x-show="adaIndustri === 'Y'">Industri</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Durasi (JP) *</label>
                                            <input type="number" 
                                                   :name="'unit_durations[' + unit.id + ']'"
                                                   :value="unitDurations[unit.id]"
                                                   @input="updateDuration(unit.id, $event.target.value)"
                                                   min="0"
                                                   required
                                                   class="w-24 px-2 py-1.5 text-sm border rounded">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                @error('selected_units')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkbox Industri -->
            <div class="flex items-center gap-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                <input type="hidden" name="ada_industri" value="N">
                <input type="checkbox" 
                       name="ada_industri" 
                       id="ada_industri" 
                       value="Y" 
                       x-model="adaIndustri"
                       class="h-5 w-5 text-orange-600 rounded">
                <label for="ada_industri" class="text-sm font-medium text-gray-800">
                    Ada Komponen Industri (Tipe "Industri" akan muncul di pilihan)
                </label>
            </div>

            <!-- Total JP -->
            <div class="bg-green-50 border-2 border-green-300 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Total Jam Pelajaran:</span>
                    <div class="text-right">
                        <span class="text-4xl font-bold text-green-700" x-text="totalJP"></span>
                        <span class="text-sm text-green-600 ml-2">jam</span>
                    </div>
                </div>
            </div>

            <!-- Instruktur -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    Instruktur Pengajar 
                    <span class="text-sm text-gray-500 font-normal">(minimal 1, pilih yang ✓ untuk penanggung jawab)</span>
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3 text-sm text-blue-800">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <strong>Cara memilih:</strong>
                            <ol class="mt-1 ml-4 list-decimal text-xs">
                                <li>Centang minimal 1 instruktur</li>
                                <li>Klik tombol <strong>"Penanggung Jawab"</strong> pada salah satu instruktur yang dicentang</li>
                                <li>Tombol akan berubah hijau tua (✓) jika sudah terpilih sebagai PJ</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2 max-h-64 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                    @foreach($instructors as $instructor)
                    <label class="flex items-center justify-between p-3 bg-white border rounded-lg hover:border-blue-400 cursor-pointer"
                           :class="selectedInstructors.includes({{ $instructor->id }}) ? 'border-blue-500 bg-blue-50' : ''">
                        <div class="flex items-center gap-3 flex-1">
                            <input type="checkbox" 
                                   name="instructors[]" 
                                   value="{{ $instructor->id }}"
                                   x-model="selectedInstructors"
                                   class="h-5 w-5 text-blue-600 rounded">
                            <div>
                                <div class="font-medium">{{ $instructor->name }}</div>
                                <div class="text-sm text-gray-500">{{ $instructor->email ?? '-' }}</div>
                            </div>
                        </div>
                        
                        <div x-show="selectedInstructors.includes({{ $instructor->id }})" style="display: none;">
                            <label class="flex items-center gap-2 px-3 py-1.5 rounded cursor-pointer transition"
                                   :class="penanggungJawab == {{ $instructor->id }} ? 'bg-green-600 text-white border-green-700' : 'bg-green-50 border border-green-300 text-green-800 hover:bg-green-100'">
                                <input type="radio" 
                                       name="penanggung_jawab" 
                                       value="{{ $instructor->id }}"
                                       x-model="penanggungJawab"
                                       class="h-4 w-4 text-green-600">
                                <span class="text-xs font-medium">
                                    <span x-show="penanggungJawab == {{ $instructor->id }}">✓ </span>Penanggung Jawab
                                </span>
                            </label>
                        </div>
                    </label>
                    @endforeach
                </div>

                @error('instructors')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('penanggung_jawab')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            </div>

            <!-- Periode & Info Lainnya -->
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ old('start_date', $program->start_date->format('Y-m-d')) }}"
                           required 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ old('end_date', $program->end_date->format('Y-m-d')) }}"
                           required 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required 
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="planned" {{ $program->status == 'planned' ? 'selected' : '' }}>Rencana</option>
                        <option value="ongoing" {{ $program->status == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                        <option value="completed" {{ $program->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Peserta</label>
                    <input type="number" 
                           name="max_participants" 
                           value="{{ old('max_participants', $program->max_participants) }}"
                           min="1" 
                           class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">JP Harian (opsional)</label>
                <input type="number" 
                       name="jp_harian" 
                       value="{{ old('jp_harian', $program->jp_harian) }}"
                       min="0" 
                       class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.programs.index') }}" 
                   class="px-6 py-2.5 border rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Update Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
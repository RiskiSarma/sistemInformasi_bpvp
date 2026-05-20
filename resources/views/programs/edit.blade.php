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

    @php
        $initMasterId    = old('master_program_id', $program->master_program_id);
        $initPaketId     = old('paket_pelatihan_id', $program->paket_pelatihan_id);
        $initMasterObj   = $masterPrograms->firstWhere('id', $initMasterId);
        $initPaketObj    = $paketPelatihans->firstWhere('id', $initPaketId);
        $initMasterLabel = $initMasterObj ? $initMasterObj->code . ' - ' . $initMasterObj->name : '';
        $initPaketLabel  = $initPaketObj
            ? ($initPaketObj->jenisPelatihan->jenis_pelatihan ?? 'Unknown') . ' - ' . $initPaketObj->tahun . ' - Batch ' . $initPaketObj->batch
            : '';
        $initJenis       = $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '';
        $initAngkatan    = old('angkatan', $program->angkatan);
        $initSelUnits    = $program->selected_units_config
            ? collect($program->selected_units_config)->pluck('unit_id')->toArray()
            : [];
        $initDurations   = $program->selected_units_config
            ? collect($program->selected_units_config)->pluck('custom_duration', 'unit_id')->toArray()
            : [];
        $initTypes       = $program->selected_units_config
            ? collect($program->selected_units_config)->pluck('type', 'unit_id')->toArray()
            : [];
        $initInstructors = $program->programInstructors->map(function($pi) {
        return $pi->instructor_id ?? $pi->pengajar_eksternal_id;
        })->filter()->values()->toArray();

        $initPJ = $program->programInstructors->where('is_penanggung_jawab', true)->first();
        $initPJ = $initPJ ? ($initPJ->instructor_id ?? $initPJ->pengajar_eksternal_id) : '';

        // Tanggal yang sudah ada
        $initStartDate = old('start_date', $program->start_date->format('Y-m-d'));
        $initEndDate   = old('end_date',   $program->end_date->format('Y-m-d'));
    @endphp

    <script>
        window._masterOptions = @json($masterProgramsData);
        window._paketOptions  = @json($paketPelatihansData);
        window._editInitData  = {
            masterId:     "{{ $initMasterId }}",
            masterLabel:  @json($initMasterLabel),
            paketId:      "{{ $initPaketId }}",
            paketLabel:   @json($initPaketLabel),
            jenis:        @json($initJenis),
            angkatan:     @json($initAngkatan),
            selUnits:     @json($initSelUnits),
            durations:    @json((object) $initDurations),
            types:        @json((object) $initTypes),
            instructors:  @json($initInstructors),
            pj:           "{{ $initPJ }}",
            totalJP:      {{ $program->total_jp_from_selected_units ?? 0 }},
            adaIndustri:  "{{ old('ada_industri', $program->ada_industri) }}",
            excludeId:    "{{ $program->id }}",
            startDate:    "{{ $initStartDate }}",
            endDate:      "{{ $initEndDate }}",
        };
    </script>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Program Pelatihan</h2>

        <form method="POST" action="{{ route('admin.programs.update', $program) }}" class="space-y-10">
            @csrf
            @method('PUT')

            <div
                x-data="programEditForm()"
                @click.outside="masterOpen = false; paketOpen = false"
            >

            {{-- Hidden inputs --}}
            <input type="hidden" name="master_program_id" :value="selectedMaster">
            <input type="hidden" name="paket_pelatihan_id" :value="selectedPaket">
            <input type="hidden" name="angkatan" :value="angkatan">
            <input type="hidden" name="penanggung_jawab" :value="penanggungJawab">

            <!-- ══════════════════════════════════════════ -->
            <!-- Searchable Dropdowns                      -->
            <!-- ══════════════════════════════════════════ -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Master Program -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Master Program <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                                </svg>
                            </span>
                            <input type="text"
                                x-model="masterSearch"
                                @focus="masterOpen = true"
                                @input="masterOpen = true; if (!masterSearch) clearMaster()"
                                @keydown.escape="masterOpen = false"
                                placeholder="Ketik untuk mencari master program..."
                                autocomplete="off"
                                class="w-full pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="selectedMaster ? 'border-blue-400 bg-blue-50' : ''"
                            >
                            <button type="button" x-show="masterSearch" @click="clearMaster()"
                                class="absolute right-3 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <span x-show="!masterSearch" class="absolute right-3 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </div>
                        <div x-show="masterOpen" x-cloak
                             class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="max-h-60 overflow-y-auto">
                                <template x-if="filteredMasters.length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-400 text-center">Tidak ada hasil</div>
                                </template>
                                <template x-for="opt in filteredMasters" :key="opt.id">
                                    <div @click="selectMaster(opt)"
                                         class="px-4 py-2.5 text-sm cursor-pointer flex items-center justify-between hover:bg-blue-50 transition-colors"
                                         :class="selectedMaster === opt.id ? 'bg-blue-50 font-medium text-blue-700' : 'text-gray-800'">
                                        <span x-text="opt.label"></span>
                                        <svg x-show="selectedMaster === opt.id" class="w-4 h-4 text-blue-600 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <div class="px-4 py-2 border-t bg-gray-50 text-xs text-gray-400 flex justify-between">
                                <span x-text="filteredMasters.length + ' program ditemukan'"></span>
                                <span>Ketik untuk menyaring</span>
                            </div>
                        </div>
                    </div>
                    <div x-show="selectedMaster" x-cloak class="mt-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs text-blue-700 font-medium" x-text="masterSearch"></span>
                    </div>
                    @error('master_program_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Paket Pelatihan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Paket Pelatihan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                                </svg>
                            </span>
                            <input type="text"
                                x-model="paketSearch"
                                @focus="paketOpen = true"
                                @input="paketOpen = true; if (!paketSearch) clearPaket()"
                                @keydown.escape="paketOpen = false"
                                placeholder="Ketik untuk mencari paket pelatihan..."
                                autocomplete="off"
                                class="w-full pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                :class="selectedPaket ? 'border-blue-400 bg-blue-50' : ''"
                            >
                            <button type="button" x-show="paketSearch" @click="clearPaket()"
                                class="absolute right-3 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            <span x-show="!paketSearch" class="absolute right-3 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </div>
                        <div x-show="paketOpen" x-cloak
                             class="absolute z-40 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="max-h-60 overflow-y-auto">
                                <template x-if="filteredPakets.length === 0">
                                    <div class="px-4 py-3 text-sm text-gray-400 text-center">Tidak ada hasil</div>
                                </template>
                                <template x-for="opt in filteredPakets" :key="opt.id">
                                    <div @click="selectPaket(opt)"
                                         class="px-4 py-2.5 text-sm cursor-pointer flex items-center justify-between hover:bg-blue-50 transition-colors"
                                         :class="selectedPaket === opt.id ? 'bg-blue-50 font-medium text-blue-700' : 'text-gray-800'">
                                        <span x-text="opt.label"></span>
                                        <svg x-show="selectedPaket === opt.id" class="w-4 h-4 text-blue-600 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                            <div class="px-4 py-2 border-t bg-gray-50 text-xs text-gray-400 flex justify-between">
                                <span x-text="filteredPakets.length + ' paket ditemukan'"></span>
                                <span>Ketik untuk menyaring</span>
                            </div>
                        </div>
                    </div>
                    <div x-show="selectedPaket" x-cloak class="mt-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs text-blue-700 font-medium" x-text="paketSearch"></span>
                    </div>
                    @error('paket_pelatihan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hasil pilihan + Angkatan -->
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Jenis Pelatihan</p>
                        <span class="px-4 py-1.5 inline-block text-sm rounded-full bg-purple-600 text-white font-medium"
                              x-text="jenisPelatihan || '-'"></span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Angkatan <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal normal-case ml-1">(otomatis dihitung)</span>
                        </p>
                        <div x-show="angkatanLoading" class="flex items-center gap-2 text-blue-600 text-sm">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span>Menghitung...</span>
                        </div>
                        <div x-show="!angkatanLoading && angkatan" class="flex items-center gap-3 flex-wrap">
                            <div class="px-5 py-2 bg-blue-600 text-white text-lg font-bold rounded-lg shadow flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Angkatan <span x-text="angkatan" class="ml-1"></span>
                            </div>
                            <span x-show="isAngkatanChanged"
                                  class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full font-medium">
                                ⚠ Angkatan berubah
                            </span>
                        </div>
                        <p x-show="angkatanInfo && !angkatanLoading" class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="angkatanInfo"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Unit Kompetensi -->
            <div x-show="availableUnits.length > 0" x-cloak class="pt-2 border-t-2 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Unit Kompetensi (dari Master Program)</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm text-yellow-800">
                    Unit kompetensi diambil dari <strong>Master Program</strong>. Edit <strong>durasi custom</strong> sesuai kebutuhan.
                </div>
                <div class="space-y-3">
                    <template x-for="unit in availableUnits" :key="unit.id">
                        <div class="border rounded-lg p-4"
                             :class="selectedUnits.includes(unit.id) ? 'border-blue-500 bg-blue-50' : 'border-gray-200'">
                            <div class="flex items-start gap-4">
                                <div class="flex items-start flex-1">
                                    <input type="checkbox" :id="'unit-' + unit.id" :value="unit.id"
                                           :checked="selectedUnits.includes(unit.id)"
                                           @change="toggleUnit(unit.id, unit)"
                                           class="mt-1 h-5 w-5 text-blue-600 border-gray-300 rounded">
                                    <label :for="'unit-' + unit.id" class="ml-3 flex-1 cursor-pointer">
                                        <div class="font-mono text-sm font-bold" x-text="unit.code"></div>
                                        <div class="text-sm text-gray-700 mt-1" x-text="unit.name"></div>
                                        <div class="text-xs text-gray-500 mt-1">JP Default: <span x-text="unit.pivot ? unit.pivot.jp : (unit.jp || 0)"></span> jam</div>
                                    </label>
                                </div>
                                <template x-if="selectedUnits.includes(unit.id)">
                                    <div class="flex items-center gap-3 bg-white p-3 rounded border flex-shrink-0">
                                        <input type="hidden" :name="'selected_units[]'" :value="unit.id">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipe</label>
                                            <select :name="'unit_types[' + unit.id + ']'" x-model="unitTypes[unit.id]"
                                                    class="px-2 py-1.5 text-sm border rounded w-28">
                                                <option value="reguler">Reguler</option>
                                                <option value="softskill">Softskill</option>
                                                <option value="skkni">SKKNI</option>
                                                <option value="industri" x-show="adaIndustri === 'Y'">Industri</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Durasi (JP) *</label>
                                            <input type="number" :name="'unit_durations[' + unit.id + ']'"
                                                   :value="unitDurations[unit.id]"
                                                   @input="updateDuration(unit.id, $event.target.value)"
                                                   min="0" required class="w-24 px-2 py-1.5 text-sm border rounded">
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

            <!-- Industri -->
            <div class="mt-8 flex items-center gap-3 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                <input type="hidden" name="ada_industri" value="N">
                <input type="checkbox" name="ada_industri" id="ada_industri" value="Y"
                       x-model="adaIndustri" class="h-5 w-5 text-orange-600 rounded">
                <label for="ada_industri" class="text-sm font-medium text-gray-800">
                    Ada Komponen Industri (Tipe "Industri" akan muncul di pilihan)
                </label>
            </div>

            <!-- Total JP -->
            <div class="mt-6 bg-green-50 border-2 border-green-300 rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">Total Jam Pelajaran:</span>
                    <div class="text-right">
                        <span class="text-4xl font-bold text-green-700" x-text="totalJP"></span>
                        <span class="text-sm text-green-600 ml-2">jam</span>
                    </div>
                </div>
            </div>

            <!-- Instruktur Pengajar -->
                        <!-- Instruktur Pengajar -->
            <div class="pt-2 border-t-2 border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    Instruktur Pengajar <span class="text-sm text-gray-500 font-normal">(minimal 1)</span>
                </h3>
                
                <div class="space-y-3 max-h-80 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                    @foreach($instructors as $instructor)
                    <div class="flex items-center justify-between p-4 bg-white border rounded-xl hover:border-blue-400 transition-all"
                         :class="selectedInstructors.includes({{ $instructor->id }}) ? 'border-blue-500 ring-1 ring-blue-200' : ''">
                        
                        <div class="flex items-center gap-4 flex-1">
                            <input type="checkbox" 
                                   name="instructors[]" 
                                   value="{{ $instructor->id }}"
                                   x-model="selectedInstructors" 
                                   class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500">

                            <div>
                                <div class="font-medium text-gray-900 flex items-center gap-2">
                                    {{ $instructor->name ?? $instructor->nama ?? '-' }}
                                    @if($instructor->type === 'external')
                                        <span class="text-xs px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full font-medium">Eksternal</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $instructor->email ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div x-show="selectedInstructors.includes({{ $instructor->id }})" class="ml-4">
                            <label class="flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer transition-all hover:bg-green-50"
                                   :class="penanggungJawab == {{ $instructor->id }} ? 'bg-green-600 text-white' : 'bg-white border border-gray-300'">
                                <input type="radio" 
                                       name="penanggung_jawab" 
                                       value="{{ $instructor->id }}"
                                       x-model="penanggungJawab" 
                                       class="h-4 w-4 text-green-600 focus:ring-green-500">
                                <span class="text-sm font-medium">Penanggung Jawab</span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>

                @error('instructors')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('penanggung_jawab')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            </div>{{-- end x-data --}}

            <!-- ══════════════════════════════════════════ -->
            <!-- Periode & Status Otomatis (Edit)          -->
            <!-- ══════════════════════════════════════════ -->
            <div class="pt-2 border-t-2 border-gray-200"
                 x-data="statusAutoCalcEdit('{{ $initStartDate }}', '{{ $initEndDate }}', '{{ $program->status }}')">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">Periode Pelatihan</h3>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date"
                               x-model="startDate"
                               @change="recalcStatus()"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date"
                               x-model="endDate"
                               @change="recalcStatus()"
                               required
                               class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- ✅ Tampilan Status Otomatis + Info Perubahan -->
                <div class="mb-6 rounded-xl border-2 p-5 transition-all"
                     :class="{
                         'bg-blue-50 border-blue-300':   autoStatus === 'planned',
                         'bg-green-50 border-green-300': autoStatus === 'ongoing',
                         'bg-gray-50 border-gray-300':   autoStatus === 'completed'
                     }">
                    <div class="flex items-center gap-4 flex-wrap">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 flex-shrink-0"
                                 :class="{
                                     'text-blue-500':  autoStatus === 'planned',
                                     'text-green-500': autoStatus === 'ongoing',
                                     'text-gray-500':  autoStatus === 'completed'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-semibold text-gray-700">Status Otomatis:</span>
                        </div>
                        <span class="px-4 py-1.5 text-sm font-bold rounded-full"
                              :class="{
                                  'bg-blue-600 text-white':   autoStatus === 'planned',
                                  'bg-green-600 text-white':  autoStatus === 'ongoing',
                                  'bg-gray-600 text-white':   autoStatus === 'completed'
                              }"
                              x-text="autoStatusLabel">
                        </span>
                        <!-- ✅ Indikator perubahan status -->
                        <span x-show="statusChanged"
                              class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-2 py-1 rounded-full font-medium">
                            ⚠ Status berubah dari "<span x-text="originalStatusLabel"></span>"
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="autoStatusDesc"></span>
                    </p>
                </div>

                {{-- Hidden input status — nilai di-set otomatis oleh JS, dikirim ke server --}}
                <input type="hidden" name="status" :value="autoStatus">

                <div class="grid grid-cols-2 gap-6 mt-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Program</label>
                        <select name="status" required
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-100 text-gray-500 cursor-not-allowed"
                                disabled>
                            <option value="planned" {{ $program->status == 'planned' ? 'selected' : '' }}>Rencana</option>
                            <option value="ongoing" {{ $program->status == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                            <option value="completed" {{ $program->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Diatur otomatis berdasarkan tanggal
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Maks. Peserta</label>
                        <input type="number" name="max_participants"
                               value="{{ old('max_participants', $program->max_participants) }}"
                               min="1" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">JP Harian (opsional)</label>
                    <input type="number" name="jp_harian"
                           value="{{ old('jp_harian', $program->jp_harian) }}"
                           min="0" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.programs.index') }}" class="px-6 py-2.5 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-8 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Update Program</button>
            </div>
        </form>
    </div>
</div>

<script>
function programEditForm() {
    const d = window._editInitData || {};
    return {
        masterOptions: window._masterOptions || [],
        paketOptions:  window._paketOptions  || [],

        /* ─── Master combobox ─── */
        masterSearch: d.masterLabel || '',
        masterOpen: false,
        selectedMaster: d.masterId || '',
        masterName: '',
        get filteredMasters() {
            const q = this.masterSearch.toLowerCase();
            return q ? this.masterOptions.filter(o => o.label.toLowerCase().includes(q)) : this.masterOptions;
        },
        selectMaster(opt) {
            this.selectedMaster = opt.id;
            this.masterName     = opt.name;
            this.masterSearch   = opt.label;
            this.masterOpen     = false;
            this.availableUnits = opt.units || [];
            this.selectedUnits  = [];
            this.unitDurations  = {};
            this.unitTypes      = {};
            this.totalJP        = 0;
            this.recomputeAngkatan();
        },
        clearMaster() {
            this.selectedMaster = ''; this.masterName = ''; this.masterSearch = '';
            this.availableUnits = []; this.selectedUnits = []; this.unitDurations = {}; this.unitTypes = {}; this.totalJP = 0;
        },

        /* ─── Paket combobox ─── */
        paketSearch: d.paketLabel || '',
        paketOpen: false,
        selectedPaket: d.paketId || '',
        jenisPelatihan: d.jenis || '',
        get filteredPakets() {
            const q = this.paketSearch.toLowerCase();
            return q ? this.paketOptions.filter(o => o.label.toLowerCase().includes(q)) : this.paketOptions;
        },
        selectPaket(opt) {
            this.selectedPaket  = opt.id;
            this.jenisPelatihan = opt.jenis;
            this.paketSearch    = opt.label;
            this.paketOpen      = false;
            this.recomputeAngkatan();
        },
        clearPaket() {
            this.selectedPaket = ''; this.jenisPelatihan = ''; this.paketSearch = '';
        },

        /* ─── Angkatan ─── */
        angkatan: d.angkatan || '',
        angkatanLoading: false,
        angkatanInfo: 'Angkatan saat ini',
        isAngkatanChanged: false,
        recomputeAngkatan() {
            if (!this.selectedMaster || !this.selectedPaket) return;
            this.angkatanLoading   = true;
            this.isAngkatanChanged = true;
            fetch(`/admin/programs/next-angkatan?master_program_id=${this.selectedMaster}&paket_pelatihan_id=${this.selectedPaket}&exclude_program_id=${d.excludeId}`)
                .then(r => r.json())
                .then(res => { this.angkatan = res.angkatan; this.angkatanInfo = res.info || ''; this.angkatanLoading = false; })
                .catch(() => { this.angkatanLoading = false; });
        },

        /* ─── Unit ─── */
        availableUnits: [],
        selectedUnits: d.selUnits || [],
        unitDurations: d.durations || {},
        unitTypes: d.types || {},
        adaIndustri: d.adaIndustri || 'N',
        totalJP: d.totalJP || 0,
        toggleUnit(unitId, unit) {
            const i = this.selectedUnits.indexOf(unitId);
            if (i > -1) { this.selectedUnits.splice(i, 1); delete this.unitDurations[unitId]; delete this.unitTypes[unitId]; }
            else { this.selectedUnits.push(unitId); this.unitDurations[unitId] = unit.pivot ? unit.pivot.jp : (unit.jp || 0); this.unitTypes[unitId] = unit.pivot ? unit.pivot.type_unit : 'reguler'; }
            this.calculateTotal();
        },
        updateDuration(unitId, val) { this.unitDurations[unitId] = parseInt(val) || 0; this.calculateTotal(); },
        calculateTotal() { this.totalJP = Object.values(this.unitDurations).reduce((s, v) => s + (parseInt(v) || 0), 0); },

        /* ─── Instruktur ─── */
        selectedInstructors: d.instructors || [],
        penanggungJawab: String(d.pj || ''),

        init() {
    if (this.selectedMaster) {
        const found = this.masterOptions.find(o => o.id === String(this.selectedMaster));
        if (found) this.availableUnits = found.units || [];
    }

    this.$nextTick(() => {
        if (this.selectedInstructors.length > 0) {
            this.penanggungJawab = String(this.selectedInstructors[0]);
        }
    });

    this.$watch('selectedInstructors', (newVal) => {
        if (newVal.length > 0) {
            this.penanggungJawab = String(newVal[0]);
        } else {
            this.penanggungJawab = '';
        }
    });
}
    };
}

/* ══════════════════════════════════════════════════════
   ✅ STATUS OTOMATIS BERDASARKAN TANGGAL (EDIT MODE)
   ══════════════════════════════════════════════════════ */
function statusAutoCalcEdit(initStart, initEnd, currentStatus) {
    const statusLabels = {
        planned:   'Rencana',
        ongoing:   'Sedang Berjalan',
        completed: 'Selesai',
    };

    return {
        startDate: initStart || '',
        endDate:   initEnd   || '',
        autoStatus: currentStatus || 'planned',
        autoStatusLabel: '',
        autoStatusDesc: '',
        originalStatus: currentStatus || 'planned',
        originalStatusLabel: statusLabels[currentStatus] || currentStatus,
        statusChanged: false,

        init() {
            // Hitung status awal dari tanggal yang sudah ada
            this.recalcStatus();
            // Reset flag perubahan setelah init (bukan perubahan user)
            this.$nextTick(() => { this.statusChanged = false; });
        },

        recalcStatus() {
            if (!this.startDate || !this.endDate) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const start = new Date(this.startDate + 'T00:00:00');
            const end   = new Date(this.endDate   + 'T00:00:00');

            let newStatus;
            if (start > today) {
                newStatus            = 'planned';
                this.autoStatusLabel = '🗓 Rencana';
                this.autoStatusDesc  = `Tanggal mulai (${this.formatDate(this.startDate)}) belum tiba. Status otomatis: Rencana.`;
            } else if (end < today) {
                newStatus            = 'completed';
                this.autoStatusLabel = '✅ Selesai';
                this.autoStatusDesc  = `Tanggal selesai (${this.formatDate(this.endDate)}) sudah lewat. Status otomatis: Selesai.`;
            } else {
                newStatus            = 'ongoing';
                this.autoStatusLabel = '🟢 Sedang Berjalan';
                this.autoStatusDesc  = `Pelatihan sedang berlangsung (${this.formatDate(this.startDate)} – ${this.formatDate(this.endDate)}).`;
            }

            // Tandai perubahan jika berbeda dari status asli
            this.statusChanged = newStatus !== this.originalStatus;
            this.autoStatus    = newStatus;
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr + 'T00:00:00');
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }
    };
}
</script>
@endsection
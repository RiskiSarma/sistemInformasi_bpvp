@extends('layouts.app')

@section('title', 'Tambah Peserta')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
<style>
.ts-wrapper .ts-control {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.4rem 0.75rem;
    background: #fff;
    min-height: 42px;
    box-shadow: none;
}
.ts-wrapper.focus .ts-control {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
}
.ts-dropdown {
    background: #fff !important;
    border: 1px solid #d1d5db !important;
    border-radius: 0.5rem !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
    z-index: 9999 !important;
}
.ts-dropdown .option {
    padding: 8px 12px;
    color: #111827;
    background: #fff;
    cursor: pointer;
}
.ts-dropdown .option:hover,
.ts-dropdown .option.active {
    background: #eff6ff !important;
    color: #1d4ed8 !important;
}
.ts-dropdown .ts-dropdown-content {
    max-height: 220px;
    overflow-y: auto;
}
.ts-dropdown input.ts-search-input,
.ts-wrapper .ts-control input {
    background: #fff !important;
    color: #111827 !important;
}
.ts-wrapper.disabled .ts-control {
    background: #f3f4f6 !important;
    cursor: not-allowed;
    opacity: 1;
}
</style>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.participants.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Peserta dari User Terdaftar</h2>

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium text-red-800">Ada kesalahan input:</span>
            </div>
            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.participants.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Pilih User <span class="text-red-500">*</span>
                </label>
                <select name="user_id" id="user_id" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Program Pelatihan <span class="text-red-500">*</span>
                </label>
                <select name="program_id" id="program_id" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('program_id') border-red-500 @enderror">
                    <option value="">Pilih Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->masterProgram?->name ?? 'Program Tidak Diketahui' }}
                            @if($program->paketPelatihan)
                                - {{ $program->paketPelatihan->nama_batch ?? $program->paketPelatihan->batch ?? $program->paketPelatihan->code ?? 'Batch Tidak Diketahui' }}
                            @else
                                - Batch Tidak Diketahui
                            @endif
                            {{ $program->masterProgram?->angkatan ? ' (' . $program->masterProgram->angkatan . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                    Jenis Kelamin <span class="text-red-500">*</span>
                </label>
                <select name="gender" id="gender" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik') }}"
                    class="w-full px-3 py-2 border rounded-lg" maxlength="16">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}"
                        class="w-full px-3 py-2 border rounded-lg" placeholder="Kota / Kabupaten">
                    @error('birth_place') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                        class="w-full px-3 py-2 border rounded-lg">
                    @error('birth_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full px-3 py-2 border rounded-lg">
            </div>

            <div>
                <label for="pendidikan_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Pendidikan Terakhir <span class="text-red-500">*</span>
                </label>
                <select name="pendidikan_id" id="pendidikan_id" required
                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('pendidikan_id') border-red-500 @enderror">
                    <option value="">Pilih Pendidikan</option>
                    @foreach(\App\Models\Pendidikan::orderBy('pendidikan')->get() as $pend)
                        <option value="{{ $pend->id }}" {{ old('pendidikan_id') == $pend->id ? 'selected' : '' }}>
                            {{ $pend->pendidikan }}
                        </option>
                    @endforeach
                </select>
                @error('pendidikan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="3"
                    class="w-full px-3 py-2 border rounded-lg">{{ old('address') }}</textarea>
            </div>

            {{-- WILAYAH --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Kabupaten/Kota</label>
                    <select id="sel_kabupaten" class="w-full border rounded-lg"></select>
                    <input type="hidden" name="asal_kabupaten" id="asal_kabupaten" value="{{ old('asal_kabupaten') }}">
                    @error('asal_kabupaten') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Kecamatan</label>
                    <select id="sel_kecamatan" class="w-full border rounded-lg"></select>
                    <input type="hidden" name="asal_kecamatan" id="asal_kecamatan" value="{{ old('asal_kecamatan') }}">
                    @error('asal_kecamatan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asal Kelurahan/Desa</label>
                    <select id="sel_kelurahan" class="w-full border rounded-lg"></select>
                    <input type="hidden" name="asal_kelurahan" id="asal_kelurahan" value="{{ old('asal_kelurahan') }}">
                    @error('asal_kelurahan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required class="w-full px-3 py-2 border rounded-lg">
                    <option value="active">Aktif</option>
                    <option value="graduated">Lulus</option>
                    <option value="dropout">Dropout</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('admin.participants.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Peserta</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cityUrl     = "{{ route('wilayah.cities') }}";
    const districtUrl = "{{ route('wilayah.districts') }}";
    const villageUrl  = "{{ route('wilayah.villages') }}";

    const initKab = document.getElementById('asal_kabupaten').value.trim();
    const initKec = document.getElementById('asal_kecamatan').value.trim();
    const initKel = document.getElementById('asal_kelurahan').value.trim();

    // ── Helper: ambil nama dari TomSelect berdasarkan value ──
    function getNameFromTs(ts, val) {
        if (!val) return '';
        const opt = Object.values(ts.options).find(o => String(o.id) === String(val));
        return opt ? opt.name : '';
    }

    // ── Helper: update options TomSelect tanpa destroy ──
    function updateTsOptions(ts, data) {
        ts.clearOptions();
        ts.addOptions(data);
        ts.refreshOptions(false);
    }

    // ── Inisialisasi TomSelect Kelurahan (disabled, kosong) ──
    const tsKel = new TomSelect('#sel_kelurahan', {
        valueField:  'id',
        labelField:  'name',
        searchField: 'name',
        options:     [],
        placeholder: 'Pilih kecamatan dulu...',
        onChange(val) {
            document.getElementById('asal_kelurahan').value = getNameFromTs(this, val);
        },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });
    tsKel.disable();

    // ── Inisialisasi TomSelect Kecamatan (disabled, kosong) ──
    const tsKec = new TomSelect('#sel_kecamatan', {
        valueField:  'id',
        labelField:  'name',
        searchField: 'name',
        options:     [],
        placeholder: 'Pilih kabupaten dulu...',
        onChange(val) {
            document.getElementById('asal_kecamatan').value = getNameFromTs(this, val);
            document.getElementById('asal_kelurahan').value = '';
            tsKel.clear(true);
            updateTsOptions(tsKel, []);
            tsKel.settings.placeholder = 'Pilih kecamatan dulu...';
            tsKel.inputState();

            if (val) {
                fetchKelurahan(val, null);
            } else {
                tsKel.disable();
            }
        },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });
    tsKec.disable();

    // ── Fetch & populate Kelurahan ──
    function fetchKelurahan(districtId, autoSelectName) {
        tsKel.disable();
        tsKel.clear(true);
        updateTsOptions(tsKel, []);
        tsKel.settings.placeholder = 'Memuat...';
        tsKel.inputState();

        fetch(`${villageUrl}?district_id=${districtId}`)
            .then(r => r.json())
            .then(data => {
                updateTsOptions(tsKel, data);
                tsKel.settings.placeholder = 'Cari atau pilih kelurahan/desa...';
                tsKel.inputState();
                tsKel.enable();

                if (autoSelectName) {
                    const found = data.find(v => v.name === autoSelectName);
                    if (found) {
                        tsKel.setValue(found.id, true);
                        document.getElementById('asal_kelurahan').value = found.name;
                    }
                }
            })
            .catch(() => {
                tsKel.settings.placeholder = 'Gagal memuat';
                tsKel.inputState();
            });
    }

    // ── Fetch & populate Kecamatan ──
    function fetchKecamatan(cityId, autoKec, autoKel) {
        tsKec.disable();
        tsKec.clear(true);
        updateTsOptions(tsKec, []);
        tsKec.settings.placeholder = 'Memuat...';
        tsKec.inputState();

        tsKel.disable();
        tsKel.clear(true);
        updateTsOptions(tsKel, []);
        tsKel.settings.placeholder = 'Pilih kecamatan dulu...';
        tsKel.inputState();
        document.getElementById('asal_kelurahan').value = '';

        fetch(`${districtUrl}?city_id=${cityId}`)
            .then(r => r.json())
            .then(data => {
                updateTsOptions(tsKec, data);
                tsKec.settings.placeholder = 'Cari atau pilih kecamatan...';
                tsKec.inputState();
                tsKec.enable();

                if (autoKec) {
                    const found = data.find(d => d.name === autoKec);
                    if (found) {
                        tsKec.setValue(found.id, true);
                        document.getElementById('asal_kecamatan').value = found.name;
                        if (autoKel) {
                            fetchKelurahan(found.id, autoKel);
                        }
                    }
                }
            })
            .catch(() => {
                tsKec.settings.placeholder = 'Gagal memuat';
                tsKec.inputState();
            });
    }

    // ── Inisialisasi TomSelect Kabupaten (dengan load/search) ──
    const tsKab = new TomSelect('#sel_kabupaten', {
        valueField:  'id',
        labelField:  'name',
        searchField: 'name',
        preload:     'focus',
        placeholder: 'Cari atau pilih kabupaten/kota...',
        load(query, callback) {
            fetch(`${cityUrl}?search=${encodeURIComponent(query)}`)
                .then(r => r.json()).then(callback).catch(() => callback());
        },
        onChange(val) {
            document.getElementById('asal_kabupaten').value = getNameFromTs(this, val);
            document.getElementById('asal_kecamatan').value = '';
            document.getElementById('asal_kelurahan').value = '';

            if (val) {
                fetchKecamatan(val, null, null);
            } else {
                tsKec.disable();
                tsKec.clear(true);
                updateTsOptions(tsKec, []);
                tsKec.settings.placeholder = 'Pilih kabupaten dulu...';
                tsKec.inputState();

                tsKel.disable();
                tsKel.clear(true);
                updateTsOptions(tsKel, []);
                tsKel.settings.placeholder = 'Pilih kecamatan dulu...';
                tsKel.inputState();
            }
        },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });

    // ── Pre-populate jika ada nilai lama (old input) ──
    if (initKab) {
        fetch(`${cityUrl}?search=${encodeURIComponent(initKab)}`)
            .then(r => r.json())
            .then(cities => {
                const city = cities.find(c => c.name === initKab);
                if (!city) return;
                tsKab.addOption(city);
                tsKab.setValue(city.id, true);
                document.getElementById('asal_kabupaten').value = city.name;
                fetchKecamatan(city.id, initKec || null, initKel || null);
            })
            .catch(e => console.error('Pre-populate error:', e));
    }
});
</script>
@endsection
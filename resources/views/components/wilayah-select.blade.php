@props([
    'kabupaten' => null,
    'kecamatan' => null,
    'kelurahan' => null,
])

{{-- Tom Select CDN --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Kabupaten/Kota --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Asal Kabupaten/Kota
        </label>
        <select id="asal_kabupaten_id" name="asal_kabupaten_id"
            class="w-full border rounded-lg">
            @if($kabupaten)
                <option value="{{ $kabupaten }}" selected>{{ $kabupaten }}</option>
            @endif
        </select>
        {{-- Simpan nama teks untuk disimpan ke DB --}}
        <input type="hidden" name="asal_kabupaten" id="asal_kabupaten_text" value="{{ $kabupaten }}">
        @error('asal_kabupaten')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kecamatan --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Asal Kecamatan
        </label>
        <select id="asal_kecamatan_id" name="asal_kecamatan_id"
            class="w-full border rounded-lg" disabled>
            @if($kecamatan)
                <option value="{{ $kecamatan }}" selected>{{ $kecamatan }}</option>
            @endif
        </select>
        <input type="hidden" name="asal_kecamatan" id="asal_kecamatan_text" value="{{ $kecamatan }}">
        @error('asal_kecamatan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Kelurahan/Desa --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Asal Kelurahan/Desa
        </label>
        <select id="asal_kelurahan_id" name="asal_kelurahan_id"
            class="w-full border rounded-lg" disabled>
            @if($kelurahan)
                <option value="{{ $kelurahan }}" selected>{{ $kelurahan }}</option>
            @endif
        </select>
        <input type="hidden" name="asal_kelurahan" id="asal_kelurahan_text" value="{{ $kelurahan }}">
        @error('asal_kelurahan')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cityUrl      = "{{ route('wilayah.cities') }}";
    const districtUrl  = "{{ route('wilayah.districts') }}";
    const villageUrl   = "{{ route('wilayah.villages') }}";

    let selectedCityId     = null;
    let selectedDistrictId = null;

    // ── Kabupaten/Kota ──────────────────────────────────────
    const tsCity = new TomSelect('#asal_kabupaten_id', {
        valueField:   'id',
        labelField:   'name',
        searchField:  'name',
        placeholder:  'Cari kabupaten/kota...',
        load: function (query, callback) {
            fetch(`${cityUrl}?search=${encodeURIComponent(query)}`)
                .then(r => r.json()).then(callback).catch(() => callback());
        },
        onChange: function (cityId) {
            const label = this.getOption(cityId)?.textContent?.trim() ?? '';
            document.getElementById('asal_kabupaten_text').value = label;
            selectedCityId = cityId;

            // Reset kecamatan & kelurahan
            tsDistrict.clear();
            tsDistrict.clearOptions();
            tsDistrict.enable();
            tsVillage.clear();
            tsVillage.clearOptions();
            tsVillage.disable();
            document.getElementById('asal_kecamatan_text').value = '';
            document.getElementById('asal_kelurahan_text').value = '';
        },
        render: {
            no_results: () => '<div class="no-results">Tidak ditemukan</div>',
        }
    });

    // ── Kecamatan ────────────────────────────────────────────
    const tsDistrict = new TomSelect('#asal_kecamatan_id', {
        valueField:  'id',
        labelField:  'name',
        searchField: 'name',
        placeholder: 'Pilih kabupaten dulu...',
        load: function (query, callback) {
            if (!selectedCityId) return callback();
            fetch(`${districtUrl}?city_id=${selectedCityId}&search=${encodeURIComponent(query)}`)
                .then(r => r.json()).then(callback).catch(() => callback());
        },
        onFocus: function () {
            // Load semua kecamatan dari kota terpilih saat dibuka
            if (selectedCityId && this.options && Object.keys(this.options).length === 0) {
                fetch(`${districtUrl}?city_id=${selectedCityId}`)
                    .then(r => r.json())
                    .then(data => {
                        this.addOptions(data);
                        this.refreshOptions(false);
                    });
            }
        },
        onChange: function (districtId) {
            const label = this.getOption(districtId)?.textContent?.trim() ?? '';
            document.getElementById('asal_kecamatan_text').value = label;
            selectedDistrictId = districtId;

            // Reset kelurahan
            tsVillage.clear();
            tsVillage.clearOptions();
            tsVillage.enable();
            document.getElementById('asal_kelurahan_text').value = '';
        },
        render: {
            no_results: () => '<div class="no-results">Tidak ditemukan</div>',
        }
    });
    tsDistrict.disable();

    // ── Kelurahan/Desa ───────────────────────────────────────
    const tsVillage = new TomSelect('#asal_kelurahan_id', {
        valueField:  'id',
        labelField:  'name',
        searchField: 'name',
        placeholder: 'Pilih kecamatan dulu...',
        load: function (query, callback) {
            if (!selectedDistrictId) return callback();
            fetch(`${villageUrl}?district_id=${selectedDistrictId}&search=${encodeURIComponent(query)}`)
                .then(r => r.json()).then(callback).catch(() => callback());
        },
        onFocus: function () {
            if (selectedDistrictId && this.options && Object.keys(this.options).length === 0) {
                fetch(`${villageUrl}?district_id=${selectedDistrictId}`)
                    .then(r => r.json())
                    .then(data => {
                        this.addOptions(data);
                        this.refreshOptions(false);
                    });
            }
        },
        onChange: function (villageId) {
            const label = this.getOption(villageId)?.textContent?.trim() ?? '';
            document.getElementById('asal_kelurahan_text').value = label;
        },
        render: {
            no_results: () => '<div class="no-results">Tidak ditemukan</div>',
        }
    });
    tsVillage.disable();
});
</script>
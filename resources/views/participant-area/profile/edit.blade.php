@extends('layouts.participant')

@section('title', 'Profil Saya')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* { font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── Tom Select Overrides ── */
.ts-wrapper .ts-control {
    border: 1.5px solid #e5e7eb;
    border-radius: 0.625rem;
    padding: 0.45rem 0.75rem;
    background: #fafafa;
    min-height: 42px;
    box-shadow: none;
    font-size: 0.875rem;
    color: #111827;
    transition: border-color .18s, box-shadow .18s, background .18s;
}
.ts-wrapper.focus .ts-control {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}
.ts-dropdown {
    background: #fff !important;
    border: 1.5px solid #e5e7eb !important;
    border-radius: 0.75rem !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    z-index: 9999 !important;
    overflow: hidden;
}
.ts-dropdown .option {
    padding: 9px 14px;
    color: #374151;
    font-size: 0.85rem;
    transition: background .12s;
}
.ts-dropdown .option:hover,
.ts-dropdown .option.active {
    background: #eef2ff !important;
    color: #4338ca !important;
}
.ts-dropdown .ts-dropdown-content { max-height: 200px; overflow-y: auto; }
.ts-wrapper.disabled .ts-control {
    background: #f3f4f6 !important;
    cursor: not-allowed;
    opacity: .75;
}

/* ── Base Input Styles ── */
.field-input {
    width: 100%;
    padding: 0.55rem 0.875rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.625rem;
    background: #fafafa;
    font-size: 0.875rem;
    color: #111827;
    transition: border-color .18s, box-shadow .18s, background .18s;
    outline: none;
}
.field-input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}
.field-input.error { border-color: #f87171; }
.field-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 0.35rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

/* ── Section Card ── */
.section-card {
    background: #fff;
    border-radius: 1.125rem;
    border: 1px solid #f0f0f0;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    padding: 1.75rem;
}
.section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}
.section-title .dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #6366f1;
    flex-shrink: 0;
}

/* ── Avatar ── */
.avatar-ring {
    background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
    border-radius: 50%;
    padding: 3px;
    display: inline-block;
}
.avatar-inner {
    background: linear-gradient(135deg, #4f46e5 0%, #818cf8 100%);
    width: 88px; height: 88px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 700; color: #fff;
    border: 3px solid #fff;
}

/* ── Info Row ── */
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.6rem 0;
    border-bottom: 1px dashed #f3f4f6;
}
.info-row:last-child { border-bottom: none; }
.info-label {
    font-size: 0.78rem;
    color: #9ca3af;
    font-weight: 500;
    flex-shrink: 0;
}
.info-value {
    font-size: 0.82rem;
    font-weight: 600;
    color: #1f2937;
    text-align: right;
}

/* ── Buttons ── */
.btn-primary {
    padding: 0.575rem 1.5rem;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .18s, transform .12s, box-shadow .18s;
    box-shadow: 0 2px 10px rgba(99,102,241,.3);
}
.btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(99,102,241,.35); }

.btn-success {
    padding: 0.575rem 1.5rem;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border-radius: 0.625rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .18s, transform .12s, box-shadow .18s;
    box-shadow: 0 2px 10px rgba(16,185,129,.3);
}
.btn-success:hover { opacity: .9; transform: translateY(-1px); }

/* ── Page Header ── */
.page-header {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    margin-bottom: 1.75rem;
}
.page-header-icon {
    width: 44px; height: 44px;
    background: #eef2ff;
    border-radius: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    color: #6366f1;
    flex-shrink: 0;
}
.page-header h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
}
.page-header p { font-size: 0.82rem; color: #9ca3af; margin-top: 1px; }

/* ── Alert ── */
.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ── Wilayah group label ── */
.group-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 0.875rem;
}
.group-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #f3f4f6;
}
</style>

<div class="max-w-5xl">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
        </div>
        <div>
            <h2>Edit Profil Saya</h2>
            <p>Kelola informasi akun dan data diri Anda</p>
        </div>
    </div>

    @if(session('error'))
    <div class="alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT COLUMN ── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Informasi Pribadi --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="dot"></span> Informasi Pribadi
                </div>

                <form action="{{ route('participant.profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
                            <label class="field-label">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="field-input @error('name') error @enderror"
                                placeholder="Nama sesuai KTP">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">NIK</label>
                            <input type="text" name="nik" value="{{ old('nik', $participant->nik ?? '') }}"
                                maxlength="16"
                                class="field-input @error('nik') error @enderror"
                                placeholder="16 digit NIK">
                            @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="field-input @error('email') error @enderror"
                                placeholder="email@contoh.com">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $participant->phone ?? '') }}"
                                class="field-input" placeholder="08xxxxxxxxxx">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Tempat Lahir</label>
                            <input type="text" name="birth_place"
                                value="{{ old('birth_place', $participant->birth_place ?? '') }}"
                                class="field-input @error('birth_place') error @enderror"
                                placeholder="Kota / Kabupaten">
                            @error('birth_place')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date"
                                value="{{ old('birth_date', $participant->birth_date ? $participant->birth_date->format('Y-m-d') : '') }}"
                                class="field-input @error('birth_date') error @enderror">
                            @error('birth_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Pendidikan Terakhir</label>
                            <select name="pendidikan_id"
                                class="field-input @error('pendidikan_id') error @enderror">
                                <option value="">-- Pilih Pendidikan --</option>
                                @foreach(\App\Models\Pendidikan::orderBy('pendidikan')->get() as $pend)
                                    <option value="{{ $pend->id }}"
                                        {{ old('pendidikan_id', $participant->pendidikan_id ?? '') == $pend->id ? 'selected' : '' }}>
                                        {{ $pend->pendidikan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pendidikan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="field-label">Jenis Kelamin</label>
                            <select name="gender"
                                class="field-input @error('gender') error @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Laki-laki" {{ old('gender', $participant->gender ?? '') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender', $participant->gender ?? '') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('gender')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                    </div>

                    {{-- Alamat --}}
                    <div class="mt-5">
                        <label class="field-label">Alamat</label>
                        <textarea name="address" rows="3"
                            class="field-input @error('address') error @enderror"
                            placeholder="Alamat lengkap sesuai KTP">{{ old('address', $participant->address ?? '') }}</textarea>
                        @error('address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Asal Wilayah --}}
                    <div class="mt-6">
                        <div class="group-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            Asal Wilayah
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="field-label">Kabupaten / Kota</label>
                                <select id="sel_kabupaten" class="w-full border rounded-lg"></select>
                                <input type="hidden" name="asal_kabupaten" id="asal_kabupaten"
                                    value="{{ old('asal_kabupaten', $participant->asal_kabupaten ?? '') }}">
                                @error('asal_kabupaten')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="field-label">Kecamatan</label>
                                <select id="sel_kecamatan" class="w-full border rounded-lg"></select>
                                <input type="hidden" name="asal_kecamatan" id="asal_kecamatan"
                                    value="{{ old('asal_kecamatan', $participant->asal_kecamatan ?? '') }}">
                                @error('asal_kecamatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="field-label">Kelurahan / Desa</label>
                                <select id="sel_kelurahan" class="w-full border rounded-lg"></select>
                                <input type="hidden" name="asal_kelurahan" id="asal_kelurahan"
                                    value="{{ old('asal_kelurahan', $participant->asal_kelurahan ?? '') }}">
                                @error('asal_kelurahan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Ganti Password --}}
            <div class="section-card">
                <div class="section-title">
                    <span class="dot" style="background:#10b981"></span> Keamanan Akun
                </div>

                <form action="{{ route('participant.profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="field-label">Password Saat Ini</label>
                            <input type="password" name="current_password" required
                                class="field-input @error('current_password') error @enderror"
                                placeholder="••••••••">
                            @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="field-label">Password Baru</label>
                                <input type="password" name="password" required
                                    class="field-input @error('password') error @enderror"
                                    placeholder="Min. 8 karakter">
                            </div>
                            <div>
                                <label class="field-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="field-input" placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-success">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── RIGHT COLUMN – Profile Card ── --}}
        <div class="space-y-5">
            <div class="section-card text-center">
                <div class="avatar-ring mx-auto" style="width:fit-content">
                    <div class="avatar-inner">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                <h4 class="mt-4 text-base font-700 text-gray-900" style="font-weight:700">{{ $user->name }}</h4>
                <p class="text-xs font-medium text-indigo-500 bg-indigo-50 inline-block px-3 py-0.5 rounded-full mt-1">Peserta Pelatihan</p>
                <p class="text-xs text-gray-400 mt-2">{{ $user->email }}</p>

                @if($participant ?? false)
                <div class="mt-5 pt-5 border-t border-gray-100 text-left space-y-1">

                    <div class="info-row">
                        <span class="info-label">NIK</span>
                        <span class="info-value">{{ $participant->nik ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tempat Lahir</span>
                        <span class="info-value">{{ $participant->birth_place ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tanggal Lahir</span>
                        <span class="info-value">
                            {{ $participant->birth_date ? $participant->birth_date->format('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Pendidikan</span>
                        <span class="info-value">{{ $participant->pendidikan->pendidikan ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kabupaten</span>
                        <span class="info-value">{{ $participant->asal_kabupaten ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kecamatan</span>
                        <span class="info-value">{{ $participant->asal_kecamatan ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kelurahan</span>
                        <span class="info-value">{{ $participant->asal_kelurahan ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Alamat</span>
                        <span class="info-value" style="max-width:160px">{{ $participant->address ?? '-' }}</span>
                    </div>

                    {{-- Program --}}
                    <div class="info-row" style="align-items:flex-start">
                        <span class="info-label">Program</span>
                        <div class="text-right space-y-1" style="max-width:180px">
                            @php
                                $allPrograms = \App\Models\Participant::where('user_id', $user->id)
                                    ->with('program.masterProgram')->get();
                            @endphp
                            @forelse($allPrograms as $p)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-xs font-medium">
                                    {{ $p->program?->masterProgram?->name ?? '-' }}
                                    @if($p->program?->angkatan)
                                        <span class="text-indigo-400">· {{ $p->program->angkatan }}</span>
                                    @endif
                                </span>
                            @empty
                                <span class="info-value">-</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        @php
                            $statusClasses = [
                                'active'    => 'bg-emerald-50 text-emerald-700',
                                'graduated' => 'bg-sky-50 text-sky-700',
                            ];
                            $cls = $statusClasses[$participant->status] ?? 'bg-red-50 text-red-700';
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-600 rounded-full {{ $cls }}" style="font-weight:600">
                            {{ ucfirst($participant->status ?? '-') }}
                        </span>
                    </div>

                </div>
                @endif
            </div>

            {{-- Quick tip card --}}
            <div class="section-card" style="background:#fafbff; border-color:#e0e7ff">
                <p class="text-xs text-indigo-400 font-semibold uppercase tracking-wide mb-2">Tips</p>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Pastikan data profil Anda selalu diperbarui agar proses administrasi pelatihan berjalan lancar.
                </p>
            </div>
        </div>

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

    function getNameFromTs(ts, val) {
        if (!val) return '';
        const opt = Object.values(ts.options).find(o => String(o.id) === String(val));
        return opt ? opt.name : '';
    }

    function updateTsOptions(ts, data) {
        ts.clearOptions();
        ts.addOptions(data);
        ts.refreshOptions(false);
    }

    const tsKel = new TomSelect('#sel_kelurahan', {
        valueField: 'id', labelField: 'name', searchField: 'name',
        options: [], placeholder: 'Pilih kecamatan dulu...',
        onChange(val) { document.getElementById('asal_kelurahan').value = getNameFromTs(this, val); },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });
    tsKel.disable();

    const tsKec = new TomSelect('#sel_kecamatan', {
        valueField: 'id', labelField: 'name', searchField: 'name',
        options: [], placeholder: 'Pilih kabupaten dulu...',
        onChange(val) {
            document.getElementById('asal_kecamatan').value = getNameFromTs(this, val);
            document.getElementById('asal_kelurahan').value = '';
            tsKel.clear(true); updateTsOptions(tsKel, []);
            tsKel.settings.placeholder = 'Pilih kecamatan dulu...'; tsKel.inputState();
            if (val) { fetchKelurahan(val, null); } else { tsKel.disable(); }
        },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });
    tsKec.disable();

    function fetchKelurahan(districtId, autoSelectName) {
        tsKel.disable(); tsKel.clear(true); updateTsOptions(tsKel, []);
        tsKel.settings.placeholder = 'Memuat...'; tsKel.inputState();
        fetch(`${villageUrl}?district_id=${districtId}`)
            .then(r => r.json()).then(data => {
                updateTsOptions(tsKel, data);
                tsKel.settings.placeholder = 'Cari atau pilih kelurahan/desa...';
                tsKel.inputState(); tsKel.enable();
                if (autoSelectName) {
                    const found = data.find(v => v.name === autoSelectName);
                    if (found) { tsKel.setValue(found.id, true); document.getElementById('asal_kelurahan').value = found.name; }
                }
            }).catch(() => { tsKel.settings.placeholder = 'Gagal memuat'; tsKel.inputState(); });
    }

    function fetchKecamatan(cityId, autoKec, autoKel) {
        tsKec.disable(); tsKec.clear(true); updateTsOptions(tsKec, []);
        tsKec.settings.placeholder = 'Memuat...'; tsKec.inputState();
        tsKel.disable(); tsKel.clear(true); updateTsOptions(tsKel, []);
        tsKel.settings.placeholder = 'Pilih kecamatan dulu...'; tsKel.inputState();
        document.getElementById('asal_kelurahan').value = '';
        fetch(`${districtUrl}?city_id=${cityId}`)
            .then(r => r.json()).then(data => {
                updateTsOptions(tsKec, data);
                tsKec.settings.placeholder = 'Cari atau pilih kecamatan...';
                tsKec.inputState(); tsKec.enable();
                if (autoKec) {
                    const found = data.find(d => d.name === autoKec);
                    if (found) {
                        tsKec.setValue(found.id, true);
                        document.getElementById('asal_kecamatan').value = found.name;
                        if (autoKel) fetchKelurahan(found.id, autoKel);
                    }
                }
            }).catch(() => { tsKec.settings.placeholder = 'Gagal memuat'; tsKec.inputState(); });
    }

    const tsKab = new TomSelect('#sel_kabupaten', {
        valueField: 'id', labelField: 'name', searchField: 'name',
        preload: 'focus', placeholder: 'Cari atau pilih kabupaten/kota...',
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
                tsKec.disable(); tsKec.clear(true); updateTsOptions(tsKec, []);
                tsKec.settings.placeholder = 'Pilih kabupaten dulu...'; tsKec.inputState();
                tsKel.disable(); tsKel.clear(true); updateTsOptions(tsKel, []);
                tsKel.settings.placeholder = 'Pilih kecamatan dulu...'; tsKel.inputState();
            }
        },
        render: { no_results: () => '<div class="px-3 py-2 text-sm text-gray-400">Tidak ditemukan</div>' }
    });

    if (initKab) {
        fetch(`${cityUrl}?search=${encodeURIComponent(initKab)}`)
            .then(r => r.json()).then(cities => {
                const city = cities.find(c => c.name === initKab);
                if (!city) return;
                tsKab.addOption(city); tsKab.setValue(city.id, true);
                document.getElementById('asal_kabupaten').value = city.name;
                fetchKecamatan(city.id, initKec || null, initKel || null);
            }).catch(e => console.error('Pre-populate error:', e));
    }
});
</script>
@endsection
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Peserta - {{ $program->masterProgram->name ?? '' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
            line-height: 1.5;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 20mm 20mm 20mm 25mm;
        }

        /* ── KOP: format baru satu gambar ── */
        .kop-wrap {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .kop-wrap img { width: 100%; height: auto; display: block; }

        /* ── Fallback kop teks ── */
        .kop-text-wrap {
            display: flex;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .kop-logo-ph {
            width: 95px; flex-shrink: 0; margin-right: 12px;
            border: 1px solid #aaa; height: 85px;
            display: flex; align-items: center; justify-content: center;
            font-size: 8pt; color: #888; text-align: center;
        }
        .kop-tc { flex: 1; text-align: center; padding-top: 4px; line-height: 1.05; }
        .kop-tc .instansi     { font-size: 9.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .kop-tc .dirjen       { font-size: 10pt;  font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .kop-tc .nama-pembinaan { font-size: 10pt; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .kop-tc .nama-lembaga { font-size: 12.5pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1.8px; margin-top: 2px; }
        .alamat-detail { text-align: center; font-size: 8pt; margin-top: 5px; line-height: 1.3; }

        /* JUDUL */
        .judul-dok { text-align: center; margin: 18px 0 6px; }
        .judul-dok h2 { font-size: 13.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .nomor-st { text-align: center; font-size: 11.5pt; margin-bottom: 18px; font-weight: bold; }

        /* Section */
        .section { margin-bottom: 10px; }
        .section-row { display: table; width: 100%; margin-bottom: 3px; }
        .section-label { display: table-cell; width: 130px; vertical-align: top; font-weight: bold; }
        .section-colon { display: table-cell; width: 20px; vertical-align: top; }
        .section-content { display: table-cell; vertical-align: top; text-align: justify; }
        .list-item { margin: 3px 0; padding-left: 24px; text-indent: -24px; }
        .memerintahkan { margin: 14px 0; text-align: center; font-weight: bold; }

        /* TTD */
        .ttd-area { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; min-width: 240px; }
        .ttd-box .ttd-tempat-tgl { margin-bottom: 8px; font-size: 11.5pt; }
        .ttd-box .ttd-jabatan { margin-bottom: 12px; font-size: 11.5pt; }
        .ttd-placeholder {
            margin: 50px 0 15px 0;
            height: 40px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11.5pt;
        }
        .ttd-box .ttd-nama { font-weight: bold; text-decoration: underline; font-size: 11.5pt; }
        .ttd-box .ttd-nip { font-size: 10.5pt; }

        /* Tabel Peserta */
        .tabel-peserta {
            width: 100%; border-collapse: collapse;
            margin: 20px 0; font-size: 10pt;
        }
        .tabel-peserta th, .tabel-peserta td {
            border: 1px solid #000; padding: 6px 8px; vertical-align: middle;
        }
        .tabel-peserta th { background: #f5f5f5; font-weight: bold; text-align: center; }

        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#1d4ed8;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#1d4ed8; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
        🖨️ Cetak / Print
    </button>
    <button onclick="window.close(); if(!window.closed) { window.history.back(); }" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">
        ← Kembali
    </button>
</div>

@php
    /* ── Embed kop sebagai base64 (sama seperti daftar-hadir) ── */
    $kopBase64   = null;
    $kopHasImage = false;

    if ($settings && $settings->logo_path) {
        $imgDisk   = storage_path('app/public/' . $settings->logo_path);
        $imgPublic = public_path('storage/' . $settings->logo_path);
        $imgFile   = file_exists($imgDisk) ? $imgDisk : (file_exists($imgPublic) ? $imgPublic : null);
        if ($imgFile) {
            $mime        = mime_content_type($imgFile);
            $kopBase64   = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgFile));
            $kopHasImage = true;
        }
    }

    /* ── Data dari settings ── */
    $tempat      = $settings->tempat_surat  ?? 'Banda Aceh';
    $jabatan     = $settings->jabatan_penandatangan ?? 'Kepala Balai';
    $ttdPengirim = $settings->ttd_pengirim  ?? '';
    $namaPengirim= $settings->nama_pengirim ?? '___________________________';
    $nipPengirim = $settings->nip_pengirim  ?? '___________________________';

    /* ── Nomor surat ── */
    $romanMonths = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V',
                    6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X',
                    11 => 'XI', 12 => 'XII'];
    $bulanRomawi = $romanMonths[now()->month];
    $nomorSurat  = str_replace(
        ['{nomor}', '{bulan}', '{tahun}'],
        ['2.8/7942', $bulanRomawi, now()->year],
        $settings->format_nomor ?? '{nomor}/LP.00.04/{bulan}/{tahun}'
    );
@endphp

<div class="page">

    {{-- ══ KOP SURAT ══ --}}
    @if($kopHasImage)
        <div class="kop-wrap">
            <img src="{{ $kopBase64 }}" alt="Kop Surat">
        </div>
    @else
        <div class="kop-text-wrap">
            <div class="kop-logo-ph">LOGO</div>
            <div class="kop-tc">
                <div class="instansi">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
                <div class="dirjen">DIREKTORAT JENDERAL</div>
                <div class="nama-pembinaan">PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
                <div class="nama-lembaga">BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
                <div class="alamat-detail">
                    Jalan Kesatria, Geuceu Komp. Banda Raya, Kota Banda Aceh 23239, Telepon (0651) 45298<br>
                    Laman : http://www.kemnaker.go.id &nbsp;|&nbsp; Email : blkbandaaceh@kemnaker.go.id
                </div>
            </div>
        </div>
    @endif

    <!-- JUDUL & NOMOR -->
    <div class="judul-dok">
        <h2>SURAT TUGAS</h2>
    </div>
    <div class="nomor-st">
         {{ $nomorSurat }}
    </div>

    <!-- MENIMBANG -->
    <div class="section">
        <div class="section-row">
            <div class="section-label">Menimbang</div>
            <div class="section-colon">:</div>
            <div class="section-content">
                bahwa dalam rangka pelaksanaan <strong>{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }}</strong>
                Batch <strong>{{ $program->angkatan ?? '-' }}</strong> pada Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun Anggaran {{ now()->year }}.
            </div>
        </div>
    </div>

    <!-- DASAR -->
    <div class="section">
        <div class="section-row">
            <div class="section-label">Dasar</div>
            <div class="section-colon">:</div>
            <div class="section-content">
                @php $dhCounter = 1; @endphp
                @for($i = 1; $i <= 3; $i++)
                    @php $field = "dasar_hukum_{$i}"; @endphp
                    @if(!empty($settings->{$field}))
                        <div class="list-item">{{ $dhCounter++ }}. {{ $settings->{$field} }};</div>
                    @endif
                @endfor
                <div class="list-item">{{ $dhCounter++ }}. Surat Keputusan Kepala Balai Pelatihan Vokasi dan Produktivitas
                    Nomor <strong>{{ $nomorSurat }}</strong>
                    tanggal <strong>{{ now()->format('d F Y') }}</strong> tentang Penyelenggaraan
                    {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }}
                    Batch {{ $program->angkatan ?? '-' }} Kejuruan
                    {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program
                    {{ $program->masterProgram->name ?? '-' }} Tahun Anggaran {{ now()->year }};</div>
                <div class="list-item">{{ $dhCounter++ }}. Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun {{ now()->year }} Nomor : 026.13.2.065106/{{ now()->year }} tanggal 24 November {{ now()->year - 1 }}.</div>
            </div>
        </div>
    </div>

    <!-- MEMERINTAHKAN -->
    <div class="memerintahkan">Memerintahkan :</div>

    <div class="section">
        <div class="section-row">
            <div class="section-label">Kepada</div>
            <div class="section-colon">:</div>
            <div class="section-content">Nama-nama peserta terlampir.</div>
        </div>
    </div>

    <div class="section">
        <div class="section-row">
            <div class="section-label">Untuk</div>
            <div class="section-colon">:</div>
            <div class="section-content">
                Mengikuti kegiatan sebagai Peserta <strong>{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }}</strong>
                Batch <strong>{{ $program->angkatan ?? '-' }}</strong>
                Kejuruan <strong>{{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }}</strong>
                Program <strong>{{ $program->masterProgram->name ?? '-' }}</strong>
                bertempat di Balai Pelatihan Vokasi dan Produktivitas Banda Aceh, Jalan Kesatria Geuceu Komplek, Kecamatan Banda Raya, Kota Banda Aceh
                selama <strong>{{ $program->jp ?? '-' }} Jam Pelatihan</strong> mulai tanggal
                <strong>{{ $program->start_date ? $program->start_date->format('d F Y') : '-' }}</strong> s.d.
                <strong>{{ $program->end_date ? $program->end_date->format('d F Y') : '-' }}</strong>.
            </div>
        </div>
    </div>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div class="ttd-tempat-tgl">{{ $tempat }}, {{ now()->format('d F Y') }}</div>
            <div class="ttd-jabatan">{{ $jabatan }},</div>
            <div class="ttd-placeholder">{{ $ttdPengirim }}</div>
            <div class="ttd-nama">{{ $namaPengirim }}</div>
            <div class="ttd-nip">NIP {{ $nipPengirim }}</div>
        </div>
    </div>

    <!-- ══ LAMPIRAN: DAFTAR NAMA PESERTA ══ -->
    <div style="page-break-before: always; padding-top: 15mm;">
        <p style="text-align:right; margin-bottom:4px; font-size:10.5pt;">Lampiran Surat Perintah Peserta</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10.5pt;">Nomor : {{ $nomorSurat }}</p>
        <p style="text-align:right; margin-bottom:20px; font-size:10.5pt;">Tanggal : {{ now()->format('d F Y') }}</p>

        <p style="text-align:center; font-weight:bold; text-transform:uppercase; margin-bottom:18px; font-size:12pt;">
            DAFTAR NAMA PESERTA
        </p>

        <table class="tabel-peserta">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA</th>
                    <th>TEMPAT &amp; TANGGAL LAHIR</th>
                    <th>JENIS<br>KELAMIN</th>
                    <th>KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($program->participants as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->name ?? '-' }}</td>
                    <td style="font-size:10pt;">
                        @php
                            $tempatlahir  = $p->place_of_birth ?? $p->tempat_lahir ?? $p->birth_place ?? '-';
                            $tanggallahir = $p->date_of_birth  ?? $p->tanggal_lahir ?? $p->birth_date ?? null;
                        @endphp
                        {{ $tempatlahir }}
                        @if($tanggallahir)
                            , {{ is_string($tanggallahir) ? \Carbon\Carbon::parse($tanggallahir)->format('d F Y') : $tanggallahir->format('d F Y') }}
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @php
                            $gender = $p->gender ?? $p->jenis_kelamin ?? '-';
                            echo strtoupper(substr($gender, 0, 4)) === 'LAKI' ? 'Laki-Laki' :
                                 (strtoupper(substr($gender, 0, 4)) === 'PERE' ? 'Perempuan' : $gender);
                        @endphp
                    </td>
                    <td></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; padding:20px; color:#888;">Belum ada peserta terdaftar</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd-area" style="margin-top:60px;">
            <div class="ttd-box">
                <div class="ttd-jabatan">{{ $jabatan }},</div>
                <div class="ttd-placeholder">{{ $ttdPengirim }}</div>
                <div class="ttd-nama">{{ $namaPengirim }}</div>
                <div class="ttd-nip">NIP {{ $nipPengirim }}</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
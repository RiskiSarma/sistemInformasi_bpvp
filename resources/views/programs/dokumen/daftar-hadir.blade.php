<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm 15mm 10mm 20mm;
        }

        /* KOP: format baru = satu gambar penuh */
        .kop-wrap {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-wrap img { width: 100%; height: auto; display: block; }

        /* Fallback kop teks */
        .kop-text-wrap {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
            gap: 10px;
        }
        .kop-logo-ph {
            width: 64px; height: 64px; flex-shrink: 0;
            border: 1px solid #aaa;
            display: flex; align-items: center; justify-content: center;
            font-size: 7.5pt; color: #888; text-align: center;
        }
        .kop-tc { flex: 1; text-align: center; line-height: 1.45; }
        .kop-tc .k1 { font-size: 8.5pt; text-transform: uppercase; }
        .kop-tc .k2 { font-size: 9.5pt; font-weight: bold; text-transform: uppercase; }
        .kop-tc .k3 { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop-tc .k4 { font-size: 7.5pt; }

        /* JUDUL */
        .judul { text-align: center; margin: 10px 0 10px; }
        .judul h2 {
            font-size: 13pt; font-weight: bold;
            text-transform: uppercase; text-decoration: underline;
        }

        /* INFO */
        .info-table {
            width: 100%; border-collapse: collapse;
            font-size: 10.5pt; margin-bottom: 10px;
        }
        .info-table td { padding: 1.5px 0; vertical-align: top; }
        .info-table .lbl { width: 55mm; }
        .info-table .sep { width: 6mm; }

        /* TABEL ABSENSI */
        .tabel-hadir {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            table-layout: fixed;
        }
        .tabel-hadir th,
        .tabel-hadir td {
            border: 1px solid #000;
            padding: 3px 3px;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }
        .tabel-hadir th { font-weight: bold; background: #fff; }
        .tabel-hadir th.hari-col {
            background: #FFFF00;
            font-size: 7.5pt;
            line-height: 1.3;
        }
        .tabel-hadir td.nama-col { text-align: left; padding-left: 5px; font-size: 9pt; }
        .tabel-hadir tbody tr { height: 18px; }

        /* TTD */
        .ttd-area {
            margin-top: 14px;
            text-align: center;
            font-size: 10.5pt;
            line-height: 1.6;
        }
        .ttd-space { height: 50px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; padding: 10mm 15mm 10mm 20mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center;padding:10px;background:#1d4ed8;">
    <button onclick="window.print()"
        style="padding:9px 28px;background:#fff;color:#1d4ed8;font-weight:bold;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
        Cetak / Print (A4 Portrait)
    </button>
    <button onclick="window.close(); if(!window.closed) { window.history.back(); }" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">
        ← Kembali
    </button>   
</div>

@php
    /* === EMBED KOP SEBAGAI BASE64 === */
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

    /* === HARI-HARI PELATIHAN === */
    $startDate = $program->start_date;
    $endDate   = $program->end_date;
    $days = [];
    if ($startDate && $endDate) {
        $cur = $startDate->copy();
        while ($cur->lte($endDate)) {
            $days[] = $cur->copy();
            $cur->addDay();
        }
    }
    $days = array_slice($days, 0, 14);

    $namaHariID = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
    ];

    /* === DATA DARI SETTINGS === */
    $kopLines = ($settings && $settings->kop_surat)
        ? array_filter(array_map('trim', explode("\n", $settings->kop_surat)))
        : [
            'KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA',
            'DIREKTORAT JENDERAL PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS',
            'BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS',
          ];

    $tempatSurat  = ($settings && $settings->tempat_surat)  ? $settings->tempat_surat  : 'Banda Aceh';
    $namaPj       = ($settings && $settings->nama_pengirim) ? $settings->nama_pengirim : null;
    $nipPj        = ($settings && $settings->nip_pengirim)  ? $settings->nip_pengirim  : null;

    if (!$namaPj || !$nipPj) {
        $pj     = isset($program->programInstructors)
            ? $program->programInstructors->where('is_penanggung_jawab', true)->first()
            : null;
        $namaPj = $namaPj ?? ($pj?->instructor->name ?? null);
        $nipPj  = $nipPj  ?? ($pj?->instructor->nip  ?? null);
    }

    /* === INFO PELATIHAN === */
    $namaProgram   = $program->masterProgram->name ?? '-';
    $totalJp       = $program->jp ?? '-';
    $jumlahPeserta = $program->participants->count();
    $kejuruan      = optional(optional($program->masterProgram)->kejuruan)->name
                     ?? optional(optional($program->paketPelatihan)->jenisPelatihan)->jenis_pelatihan
                     ?? '-';
    $periodeStr    = ($program->start_date && $program->end_date)
                     ? $program->start_date->format('d F Y') . ' - ' . $program->end_date->format('d F Y')
                     : '-';
    $bulan         = $program->start_date ? $program->start_date->translatedFormat('F') : '-';
    $mingguKe      = $program->angkatan ?? 'I (Satu)';

    /* === BARIS TABEL: selalu 16 baris === */
    $participants = $program->participants;
    $totalRows    = 16;

    /* === LEBAR KOLOM DINAMIS === */
    $nDays = count($days);
    $noW   = 6;
    $namaW = max(22, 52 - ($nDays * 3));
    $hariW = $nDays > 0 ? round((100 - $noW - $namaW) / $nDays, 1) : 10;
@endphp

<div class="page">

    {{-- KOP SURAT --}}
    @if($kopHasImage)
        <div class="kop-wrap">
            <img src="{{ $kopBase64 }}" alt="Kop Surat">
        </div>
    @else
        <div class="kop-text-wrap">
            <div class="kop-logo-ph">LOGO</div>
            <div class="kop-tc">
                @foreach(array_values($kopLines) as $i => $line)
                    @if($i === 0)<div class="k1">{{ $line }}</div>
                    @elseif($i === 1)<div class="k2">{{ $line }}</div>
                    @elseif($i === 2)<div class="k3">{{ $line }}</div>
                    @else<div class="k4">{{ $line }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- JUDUL --}}
    <div class="judul">
        <h2>Daftar Hadir Peserta Apel Pagi</h2>
    </div>

    {{-- INFO PELATIHAN --}}
    <table class="info-table">
        <tr><td class="lbl">Nama Pelatihan</td>      <td class="sep">:</td><td>{{ $namaProgram }}</td></tr>
        <tr><td class="lbl">Jumlah Jam Pelatihan</td><td class="sep">:</td><td>{{ $totalJp }} JP</td></tr>
        <tr><td class="lbl">Jumlah Peserta</td>      <td class="sep">:</td><td>{{ $jumlahPeserta }} orang</td></tr>
        <tr><td class="lbl">Kejuruan</td>            <td class="sep">:</td><td>{{ $kejuruan }}</td></tr>
        <tr><td class="lbl">Tanggal Pelaksanaan</td> <td class="sep">:</td><td>{{ $periodeStr }}</td></tr>
        <tr><td class="lbl">Bulan</td>               <td class="sep">:</td><td>{{ $bulan }}</td></tr>
        <tr><td class="lbl">Minggu ke -</td>         <td class="sep">:</td><td>{{ $mingguKe }}</td></tr>
    </table>

    {{-- TABEL ABSENSI --}}
    <table class="tabel-hadir">
        <colgroup>
            <col style="width:{{ $noW }}%;">
            <col style="width:{{ $namaW }}%;">
            @foreach($days as $d)
            <col style="width:{{ $hariW }}%;">
            @endforeach
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO.</th>
                <th rowspan="2">NAMA</th>
                @foreach($days as $d)
                <th class="hari-col">
                    {{ $namaHariID[$d->englishDayOfWeek] ?? $d->format('D') }},<br>
                    {{ $d->format('d') }}<br>
                    {{ $d->translatedFormat('F') }}<br>
                    {{ $d->format('Y') }}
                </th>
                @endforeach
            </tr>
            <tr></tr>
        </thead>
        <tbody>
            @for($i = 0; $i < $totalRows; $i++)
            @php $p = $participants->get($i); @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="nama-col">{{ $p ? ($p->name ?? '') : '' }}</td>
                @foreach($days as $d)
                <td></td>
                @endforeach
            </tr>
            @endfor
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="ttd-area">
        <p>Mengetahui,</p>
        <p>{{ $tempatSurat }}, {{ ($program->end_date ?? now())->format('d F Y') }}</p>
        <p>Penanggung Jawab Kelas,</p>
        <div class="ttd-space"></div>
        <p class="ttd-nama">{{ $namaPj ?? '______________________________' }}</p>
        <p>NIP. {{ $nipPj ?? '______________________________' }}</p>
    </div>

</div>
</body>
</html>
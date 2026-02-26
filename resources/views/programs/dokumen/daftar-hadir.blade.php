<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5pt; color: #000; background: #fff; }
        .page { width: 297mm; min-height: 210mm; margin: 0 auto; padding: 12mm 15mm 12mm 20mm; }

        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 12px; }
        .kop-logo { width: 60px; height: 60px; margin-right: 12px; border: 2px solid #333; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size:9px; color:#555; text-align:center; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .nama-lembaga { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 2px 0; }
        .kop-text .instansi { font-size: 8.5pt; text-transform: uppercase; }
        .kop-text .alamat { font-size: 8pt; color: #333; }

        .judul-dok { text-align: center; margin: 10px 0 8px; }
        .judul-dok h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }

        .info-bar { display: flex; gap: 20px; margin-bottom: 10px; font-size: 9.5pt; flex-wrap: wrap; }
        .info-item { display: flex; gap: 0; }
        .info-item .lbl { min-width: 100px; }
        .info-item .sep { min-width: 14px; }

        /* Landscape absensi table */
        .tabel-hadir { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .tabel-hadir th { background: #1d4ed8; color: #fff; border: 1px solid #1e40af; padding: 4px 5px; text-align: center; font-weight: bold; white-space: nowrap; }
        .tabel-hadir th.hari { background: #1e40af; font-size: 7.5pt; }
        .tabel-hadir td { border: 1px solid #999; padding: 4px 5px; vertical-align: middle; }
        .tabel-hadir td:first-child { text-align: center; }
        .tabel-hadir tr:nth-child(even) td { background: #f0f4ff; }
        .td-sign { width: 28px; min-width: 28px; height: 24px; }

        .ttd-area { margin-top: 16px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; min-width: 180px; }
        .ttd-box .ttd-nama { font-weight: bold; text-decoration: underline; }
        .ttd-box .ttd-nip { font-size: 9pt; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 9.5pt; }
            .page { margin: 0; width: 297mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#0d9488;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#0d9488; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">🖨️ Cetak / Print (A4 Landscape)</button>
    <button onclick="window.history.back()" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">← Kembali</button>
</div>

@php
    // Hitung jumlah hari pelatihan (untuk header kolom)
    $startDate = $program->start_date;
    $endDate   = $program->end_date;
    $days = [];
    if ($startDate && $endDate) {
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            // Skip Sabtu & Minggu (opsional, hapus jika perlu)
            // if (!$current->isWeekend()) {
                $days[] = $current->copy();
            // }
            $current->addDay();
        }
    }
    // Batasi 30 hari tampilan agar tidak overflow
    $days = array_slice($days, 0, 30);
    $namaHari = ['Sun' => 'Mg', 'Mon' => 'Sn', 'Tue' => 'Sl', 'Wed' => 'Rb', 'Thu' => 'Km', 'Fri' => 'Jm', 'Sat' => 'Sb'];
@endphp

<div class="page">

    <!-- KOP -->
    <div class="kop">
        <div class="kop-logo">LOGO</div>
        <div class="kop-text">
            <div class="instansi">Kementerian / Lembaga / Instansi</div>
            <div class="nama-lembaga">Nama Lembaga Pelatihan</div>
            <div class="alamat">Jl. Alamat Lembaga No. 1, Kota, Provinsi | Telp. (000) 000-0000</div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul-dok">
        <h2>Daftar Hadir Peserta Pelatihan</h2>
        <p style="font-size:11pt; margin-top:3px;">{{ $program->masterProgram->name ?? '-' }} — Angkatan {{ $program->angkatan ?? '-' }}</p>
    </div>

    <!-- INFO BAR -->
    <div class="info-bar">
        <div class="info-item"><span class="lbl">Program</span><span class="sep">:</span><span><strong>{{ $program->masterProgram->name ?? '-' }}</strong></span></div>
        <div class="info-item"><span class="lbl">Angkatan</span><span class="sep">:</span><span>{{ $program->angkatan ?? '-' }}</span></div>
        <div class="info-item"><span class="lbl">Periode</span><span class="sep">:</span><span>{{ $program->start_date ? $program->start_date->format('d M Y') : '-' }} s/d {{ $program->end_date ? $program->end_date->format('d M Y') : '-' }}</span></div>
        <div class="info-item"><span class="lbl">Jumlah Peserta</span><span class="sep">:</span><span>{{ $program->participants->count() }} orang</span></div>
    </div>

    <!-- TABEL ABSENSI -->
    <table class="tabel-hadir">
        <thead>
            <tr>
                <th rowspan="2" style="width:30px">No</th>
                <th rowspan="2" style="min-width:140px">Nama Peserta</th>
                <th rowspan="2" style="width:100px">NIK / NIP</th>
                <th rowspan="2" style="width:80px">Instansi</th>
                @foreach($days as $d)
                <th class="hari" style="width:28px">
                    {{ $namaHari[$d->format('D')] ?? $d->format('D') }}<br>
                    <span style="font-weight:normal;">{{ $d->format('d') }}</span>
                </th>
                @endforeach
                <th rowspan="2" style="width:50px">Hadir</th>
                <th rowspan="2" style="width:50px">Absen</th>
                <th rowspan="2" style="width:60px">Ket</th>
            </tr>
            <tr>
                <!-- sub baris sudah di-rowspan -->
            </tr>
        </thead>
        <tbody>
            @forelse($program->participants as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->name ?? '-' }}</td>
                <td style="font-size:8pt;">{{ $p->nik ?? $p->nip ?? '-' }}</td>
                <td style="font-size:8pt;">{{ $p->instansi ?? '-' }}</td>
                @foreach($days as $d)
                <td class="td-sign"></td>
                @endforeach
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 7 + count($days) }}" style="text-align:center; color:#888; padding:16px;">Belum ada peserta terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if(count($days) >= 30)
    <p style="font-size:8.5pt; color:#666; margin-top:6px; font-style:italic;">* Menampilkan 30 hari pertama. Untuk periode lebih panjang, cetak per minggu.</p>
    @endif

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div style="margin-bottom:4px;">_____________________, {{ now()->format('d F Y') }}</div>
            <div style="margin-bottom:4px;">Mengetahui,</div>
            <div style="margin-bottom:60px;">Koordinator Pelatihan</div>
            <div style="height:50px;"></div>
            @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
            <div class="ttd-nama">{{ $pj->instructor->name ?? '___________________________' }}</div>
            <div class="ttd-nip">NIP. {{ $pj->instructor->nip ?? '___________________________' }}</div>
        </div>
    </div>

</div>
</body>
</html>
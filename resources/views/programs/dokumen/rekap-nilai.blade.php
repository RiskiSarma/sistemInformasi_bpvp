<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; color: #000; background: #fff; }
        .page { width: 297mm; min-height: 210mm; margin: 0 auto; padding: 12mm 15mm 12mm 20mm; }

        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 7px; margin-bottom: 12px; }
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
        .info-item .sep { min-width: 12px; }

        /* Tabel Nilai - landscape */
        .tabel-nilai { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .tabel-nilai th { background: #1d4ed8; color: #fff; border: 1px solid #1e40af; padding: 5px 5px; text-align: center; font-weight: bold; }
        .tabel-nilai th.sub { background: #3b82f6; font-size: 8pt; }
        .tabel-nilai td { border: 1px solid #999; padding: 4px 5px; vertical-align: middle; text-align: center; }
        .tabel-nilai td.nama-col { text-align: left; }
        .tabel-nilai tr:nth-child(even) td { background: #f0f4ff; }

        /* Status kelulusan */
        .lulus { color: #16a34a; font-weight: bold; }
        .tidak-lulus { color: #dc2626; font-weight: bold; }

        /* Rata-rata row */
        .avg-row td { background: #fef9c3 !important; font-weight: bold; border-top: 2px solid #ca8a04; }

        .keterangan-box { margin-top: 12px; font-size: 9.5pt; border: 1px solid #ccc; padding: 8px 12px; border-radius: 4px; background: #f9fafb; }
        .keterangan-box p { margin-bottom: 3px; }

        .ttd-area { margin-top: 16px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; min-width: 170px; }
        .ttd-box .ttd-nama { font-weight: bold; text-decoration: underline; }
        .ttd-box .ttd-nip { font-size: 9pt; }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; width: 297mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#be123c;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#be123c; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">🖨️ Cetak / Print (A4 Landscape)</button>
    <button onclick="window.history.back()" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">← Kembali</button>
</div>

@php
    // Ambil daftar unit untuk header kolom
    $units = collect($unitsData ?? []);
    $nilaiLulus = 70; // nilai minimum lulus
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
        <h2>Rekap Nilai Peserta Pelatihan</h2>
        <p style="font-size:11pt; margin-top:3px;">{{ $program->masterProgram->name ?? '-' }} — Angkatan {{ $program->angkatan ?? '-' }}</p>
    </div>

    <!-- INFO BAR -->
    <div class="info-bar">
        <div class="info-item"><span class="lbl">Program</span><span class="sep">:</span><span><strong>{{ $program->masterProgram->name ?? '-' }}</strong></span></div>
        <div class="info-item"><span class="lbl">Angkatan</span><span class="sep">:</span><span>{{ $program->angkatan ?? '-' }}</span></div>
        <div class="info-item"><span class="lbl">Periode</span><span class="sep">:</span><span>{{ $program->start_date ? $program->start_date->format('d M Y') : '-' }} s/d {{ $program->end_date ? $program->end_date->format('d M Y') : '-' }}</span></div>
        <div class="info-item"><span class="lbl">Nilai Lulus</span><span class="sep">:</span><span>≥ {{ $nilaiLulus }}</span></div>
        <div class="info-item"><span class="lbl">Peserta</span><span class="sep">:</span><span>{{ $program->participants->count() }} orang</span></div>
    </div>

    <!-- TABEL NILAI -->
    <table class="tabel-nilai">
        <thead>
            <tr>
                <th rowspan="2" style="width:30px">No</th>
                <th rowspan="2" style="min-width:130px; text-align:left; padding-left:7px;">Nama Peserta</th>
                <th rowspan="2" style="width:90px">NIK / NIP</th>
                @foreach($units->take(10) as $unit)
                <th style="min-width:55px; font-size:7.5pt; white-space:normal; max-width:70px;">
                    {{ \Illuminate\Support\Str::limit($unit['unit']->name ?? '-', 20) }}
                    <div style="font-size:6.5pt; font-weight:normal; margin-top:2px;">({{ $unit['custom_duration'] ?? 0 }} JP)</div>
                </th>
                @endforeach
                <th rowspan="2" style="width:55px">Rata-rata</th>
                <th rowspan="2" style="width:55px">Status</th>
                <th rowspan="2" style="width:60px">Predikat</th>
            </tr>
            <tr>
                @foreach($units->take(10) as $unit)
                <th class="sub" style="font-size:7pt;">{{ $unit['unit']->code ?? '-' }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($program->participants as $i => $p)
            @php
                // Nilai kosong (placeholder) - implementasi nilai nyata tergantung model
                $nilaiList = [];
                foreach($units->take(10) as $u) {
                    $nilaiList[] = null; // kosong, diisi manual atau dari model nilai
                }
                $rataRata = null;
                $status = '-';
                $predikat = '-';
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="nama-col" style="text-align:left; padding-left:7px;">{{ $p->name ?? '-' }}</td>
                <td>{{ $p->nik ?? $p->nip ?? '-' }}</td>
                @foreach($nilaiList as $nilai)
                <td style="height:24px;"></td>
                @endforeach
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 6 + $units->take(10)->count() }}" style="text-align:center; color:#888; padding:16px;">Belum ada peserta terdaftar</td>
            </tr>
            @endforelse

            <!-- Baris rata-rata kelas -->
            @if($program->participants->count() > 0)
            <tr class="avg-row">
                <td colspan="3" style="text-align:right; padding-right:8px;">RATA-RATA KELAS</td>
                @foreach($units->take(10) as $u)
                <td></td>
                @endforeach
                <td></td>
                <td colspan="2"></td>
            </tr>
            @endif
        </tbody>
    </table>

    @if($units->count() > 10)
    <p style="font-size:8.5pt; color:#666; margin-top:5px; font-style:italic;">
        * Menampilkan 10 unit pertama dari total {{ $units->count() }} unit. Lihat halaman berikutnya untuk unit selanjutnya.
    </p>
    @endif

    <!-- KETERANGAN -->
    <div class="keterangan-box">
        <p><strong>Keterangan Predikat:</strong></p>
        <p>90 – 100 = Sangat Memuaskan &nbsp;|&nbsp; 80 – 89 = Memuaskan &nbsp;|&nbsp; 70 – 79 = Cukup &nbsp;|&nbsp; &lt; 70 = Tidak Lulus</p>
    </div>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box" style="text-align:left;">
            <p>Mengetahui,</p>
            <p style="margin-bottom:4px;">Kepala / Pimpinan Lembaga</p>
            <div style="height:55px;"></div>
            <div class="ttd-nama">___________________________</div>
            <div class="ttd-nip">NIP. ___________________________</div>
        </div>
        <div class="ttd-box">
            <p>_____________________, {{ now()->format('d F Y') }}</p>
            <p style="margin-bottom:4px;">Koordinator Pelatihan,</p>
            <div style="height:55px;"></div>
            @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
            <div class="ttd-nama">{{ $pj->instructor->name ?? '___________________________' }}</div>
            <div class="ttd-nip">NIP. {{ $pj->instructor->nip ?? '___________________________' }}</div>
        </div>
    </div>

</div>
</body>
</html>
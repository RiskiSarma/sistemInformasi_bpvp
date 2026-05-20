<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelatihan - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm 20mm 15mm 25mm; }

        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 14px; }
        .kop-logo { width: 65px; height: 65px; margin-right: 14px; border: 2px solid #333; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size:9px; color:#555; text-align:center; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .nama-lembaga { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 2px 0; }
        .kop-text .instansi { font-size: 9pt; text-transform: uppercase; }
        .kop-text .alamat { font-size: 8pt; color: #333; }

        .judul-dok { text-align: center; margin: 14px 0 10px; }
        .judul-dok h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; letter-spacing: 1px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 20px; margin-bottom: 14px; font-size: 10.5pt; }
        .info-row { display: flex; gap: 0; }
        .info-row .lbl { min-width: 130px; }
        .info-row .sep { min-width: 16px; }

        /* Jadwal Table */
        .tabel-jadwal { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .tabel-jadwal th { background: #2563eb; color: #fff; border: 1px solid #1d4ed8; padding: 6px 7px; text-align: center; font-weight: bold; }
        .tabel-jadwal td { border: 1px solid #999; padding: 5px 7px; vertical-align: middle; }
        .tabel-jadwal tr:nth-child(even) td { background: #f8faff; }
        .tabel-jadwal td:first-child { text-align: center; }
        .tabel-jadwal td:last-child { text-align: center; }

        .badge-type {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 9pt;
            font-weight: bold;
        }
        .badge-reguler { background: #dbeafe; color: #1d4ed8; }
        .badge-softskill { background: #ede9fe; color: #7c3aed; }
        .badge-skkni { background: #d1fae5; color: #065f46; }
        .badge-industri { background: #ffedd5; color: #c2410c; }

        .total-row td { background: #f0f4ff !important; font-weight: bold; }

        .ttd-area { margin-top: 24px; display: flex; justify-content: space-between; }
        .ttd-box { text-align: center; min-width: 180px; }
        .ttd-box .ttd-nama { font-weight: bold; text-decoration: underline; }
        .ttd-box .ttd-nip { font-size: 9.5pt; }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#ea580c;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#ea580c; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">🖨️ Cetak / Print</button>
    <button onclick="window.close(); if(!window.closed) { window.history.back(); }" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">← Kembali</button>
</div>

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
        <h2>Jadwal Pelatihan</h2>
        <p style="font-size:12pt; margin-top:4px;">{{ $program->masterProgram->name ?? 'Nama Program' }}</p>
        <p style="font-size:10.5pt; color:#555;">Angkatan {{ $program->angkatan ?? '-' }} &bull; {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }}</p>
    </div>

    <!-- INFO PROGRAM -->
    <div style="border: 1px solid #d1d5db; border-radius: 4px; padding: 10px 14px; margin-bottom: 14px; background: #f9fafb; font-size: 10.5pt;">
        <div class="info-grid">
            <div class="info-row"><span class="lbl">Program</span><span class="sep">:</span><span><strong>{{ $program->masterProgram->name ?? '-' }}</strong></span></div>
            <div class="info-row"><span class="lbl">Tanggal Mulai</span><span class="sep">:</span><span>{{ $program->start_date ? $program->start_date->format('d F Y') : '-' }}</span></div>
            <div class="info-row"><span class="lbl">Angkatan</span><span class="sep">:</span><span>{{ $program->angkatan ?? '-' }}</span></div>
            <div class="info-row"><span class="lbl">Tanggal Selesai</span><span class="sep">:</span><span>{{ $program->end_date ? $program->end_date->format('d F Y') : '-' }}</span></div>
            <div class="info-row"><span class="lbl">Jenis Pelatihan</span><span class="sep">:</span><span>{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }}</span></div>
            <div class="info-row"><span class="lbl">Total JP</span><span class="sep">:</span><span><strong>{{ $program->jp ?? $program->total_jp_from_selected_units ?? '-' }} JP</strong> ({{ $program->jp_harian ? $program->jp_harian . ' JP/hari' : '-' }})</span></div>
            <div class="info-row"><span class="lbl">Instruktur PJ</span><span class="sep">:</span>
                <span>
                    @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
                    {{ $pj->instructor->name ?? '-' }}
                </span>
            </div>
            <div class="info-row"><span class="lbl">Jumlah Instruktur</span><span class="sep">:</span><span>{{ $program->programInstructors->count() }} orang</span></div>
        </div>
    </div>

    <!-- TABEL JADWAL UNIT -->
    <p style="font-weight:bold; margin-bottom:6px; font-size:11pt;">Daftar Unit Kompetensi / Mata Pelajaran:</p>
    <table class="tabel-jadwal">
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th>Kode Unit</th>
                <th>Nama Unit Kompetensi / Mata Pelajaran</th>
                <th style="width:70px">Tipe</th>
                <th style="width:55px">JP</th>
                <th>Instruktur</th>
                <th style="width:80px">Periode</th>
            </tr>
        </thead>
        <tbody>
            @php $totalJP = 0; @endphp
            @forelse($unitsData as $i => $item)
            @php
                $totalJP += $item['custom_duration'] ?? 0;
                $typeClass = match($item['type'] ?? 'reguler') {
                    'softskill' => 'badge-softskill',
                    'skkni' => 'badge-skkni',
                    'industri' => 'badge-industri',
                    default => 'badge-reguler',
                };
                $typeLabel = match($item['type'] ?? 'reguler') {
                    'softskill' => 'Softskill',
                    'skkni' => 'SKKNI',
                    'industri' => 'Industri',
                    default => 'Reguler',
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="font-family:monospace; font-size:9pt;">{{ $item['unit']->code ?? '-' }}</td>
                <td>{{ $item['unit']->name ?? '-' }}</td>
                <td style="text-align:center;"><span class="badge-type {{ $typeClass }}">{{ $typeLabel }}</span></td>
                <td style="text-align:center; font-weight:bold;">{{ $item['custom_duration'] ?? 0 }}</td>
                <td>{{ $program->programInstructors->first()->instructor->name ?? '-' }}</td>
                <td style="font-size:9pt; text-align:center;">-</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#888; padding:16px;">Belum ada unit kompetensi</td>
            </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="4" style="text-align:right;">TOTAL JAM PELAJARAN</td>
                <td style="text-align:center;">{{ $totalJP }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <!-- INSTRUKTUR PENGAJAR -->
    @if($program->programInstructors->count() > 1)
    <p style="font-weight:bold; margin: 14px 0 6px; font-size:11pt;">Tim Instruktur:</p>
    <table class="tabel-jadwal">
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th>Nama Instruktur</th>
                <th>NIP / No. ID</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($program->programInstructors as $i => $pi)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $pi->instructor->name ?? '-' }}</td>
                <td>{{ $pi->instructor->nip ?? '-' }}</td>
                <td>{{ $pi->is_penanggung_jawab ? 'Penanggung Jawab' : 'Instruktur' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- TTD -->
    <div class="ttd-area" style="margin-top: 24px;">
        <div class="ttd-box" style="text-align:left;">
            <p>Mengetahui,</p>
            <p style="margin-bottom:60px;">Kepala / Pimpinan Lembaga</p>
            <div style="height:60px;"></div>
            <div class="ttd-nama">___________________________</div>
            <div class="ttd-nip">NIP. ___________________________</div>
        </div>
        <div class="ttd-box">
            <p>_____________________, {{ now()->format('d F Y') }}</p>
            <p style="margin-bottom:4px;">Koordinator Pelatihan,</p>
            <div style="height:60px;"></div>
            @if($pj)
            <div class="ttd-nama">{{ $pj->instructor->name ?? '___________________________' }}</div>
            <div class="ttd-nip">NIP. {{ $pj->instructor->nip ?? '___________________________' }}</div>
            @else
            <div class="ttd-nama">___________________________</div>
            <div class="ttd-nip">NIP. ___________________________</div>
            @endif
        </div>
    </div>

</div>
</body>
</html>
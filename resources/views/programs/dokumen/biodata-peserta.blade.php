<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Peserta - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; background: #fff; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 18mm 22mm 18mm 28mm; }

        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 7px; margin-bottom: 12px; }
        .kop-logo { width: 65px; height: 65px; margin-right: 14px; border: 2px solid #333; border-radius: 50%; display:flex; align-items:center; justify-content:center; font-size:9px; color:#555; text-align:center; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .nama-lembaga { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin: 2px 0; }
        .kop-text .instansi { font-size: 8.5pt; text-transform: uppercase; }
        .kop-text .alamat { font-size: 8pt; color: #333; }

        .judul-dok { text-align: center; margin: 12px 0 10px; }
        .judul-dok h2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }

        /* Info Header */
        .info-header { border: 1px solid #ccc; border-radius: 4px; padding: 8px 12px; margin-bottom: 14px; background: #f9fafb; font-size: 10.5pt; }
        .info-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 3px 20px; }
        .info-row { display: flex; }
        .info-row .lbl { min-width: 120px; }
        .info-row .sep { min-width: 14px; }

        /* Biodata Card - tiap peserta satu blok */
        .peserta-card {
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .peserta-card-header {
            background: #1d4ed8;
            color: #fff;
            padding: 5px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 4px 4px 0 0;
        }
        .peserta-card-header .nomor { font-size: 11pt; font-weight: bold; min-width: 28px; }
        .peserta-card-header .nama { font-size: 11pt; font-weight: bold; flex: 1; }
        .peserta-card-body { padding: 8px 12px; }
        .peserta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3px 20px; font-size: 10.5pt; }
        .peserta-row { display: flex; }
        .peserta-row .lbl { min-width: 130px; color: #555; }
        .peserta-row .sep { min-width: 14px; }
        .peserta-row .val { flex: 1; }

        /* Tabel ringkasan (alternatif) */
        .tabel-biodata { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .tabel-biodata th { background: #1d4ed8; color: #fff; border: 1px solid #1e40af; padding: 5px 7px; text-align: center; font-weight: bold; }
        .tabel-biodata td { border: 1px solid #999; padding: 5px 7px; vertical-align: top; }
        .tabel-biodata td:first-child { text-align: center; }
        .tabel-biodata tr:nth-child(even) td { background: #f0f4ff; }

        .ttd-area { margin-top: 20px; display: flex; justify-content: flex-end; }
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

<div class="no-print" style="text-align:center; padding:12px; background:#4f46e5;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#4f46e5; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">🖨️ Cetak / Print</button>
    <button onclick="window.history.back()" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">← Kembali</button>
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
        <h2>Biodata Peserta Pelatihan</h2>
        <p style="font-size:11pt; margin-top:3px;">{{ $program->masterProgram->name ?? '-' }}</p>
    </div>

    <!-- INFO HEADER -->
    <div class="info-header">
        <div class="info-grid-2">
            <div class="info-row"><span class="lbl">Program</span><span class="sep">:</span><span><strong>{{ $program->masterProgram->name ?? '-' }}</strong></span></div>
            <div class="info-row"><span class="lbl">Periode</span><span class="sep">:</span><span>{{ $program->start_date ? $program->start_date->format('d F Y') : '-' }} s/d {{ $program->end_date ? $program->end_date->format('d F Y') : '-' }}</span></div>
            <div class="info-row"><span class="lbl">Angkatan</span><span class="sep">:</span><span>{{ $program->angkatan ?? '-' }}</span></div>
            <div class="info-row"><span class="lbl">Jenis Pelatihan</span><span class="sep">:</span><span>{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '-' }}</span></div>
            <div class="info-row"><span class="lbl">Jumlah Peserta</span><span class="sep">:</span><span><strong>{{ $program->participants->count() }} orang</strong></span></div>
        </div>
    </div>

    <!-- TABEL RINGKASAN BIODATA -->
    <p style="font-weight:bold; margin-bottom:8px; font-size:11pt;">Rekap Data Peserta:</p>
    <table class="tabel-biodata">
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th>Nama Lengkap</th>
                <th>NIK / NIP</th>
                <th>Tempat, Tgl Lahir</th>
                <th>Pendidikan</th>
                <th>Instansi / Perusahaan</th>
                <th>Jabatan</th>
                <th>No. HP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($program->participants as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->name ?? '-' }}</td>
                <td style="font-size:9.5pt;">{{ $p->nik ?? $p->nip ?? '-' }}</td>
                <td style="font-size:9.5pt;">
                    @if(isset($p->tempat_lahir) || isset($p->tanggal_lahir))
                        {{ $p->tempat_lahir ?? '-' }}, {{ isset($p->tanggal_lahir) ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '-' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $p->pendidikan ?? $p->education ?? '-' }}</td>
                <td>{{ $p->instansi ?? $p->perusahaan ?? $p->company ?? '-' }}</td>
                <td>{{ $p->jabatan ?? $p->position ?? '-' }}</td>
                <td style="font-size:9.5pt;">{{ $p->phone ?? $p->no_hp ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#888; padding:16px;">Belum ada peserta terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div style="margin-bottom:4px;">_____________________, {{ now()->format('d F Y') }}</div>
            <div style="margin-bottom:4px;">Koordinator Pelatihan,</div>
            <div style="height:60px;"></div>
            @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
            <div class="ttd-nama">{{ $pj->instructor->name ?? '___________________________' }}</div>
            <div class="ttd-nip">NIP. {{ $pj->instructor->nip ?? '___________________________' }}</div>
        </div>
    </div>

</div>
</body>
</html>
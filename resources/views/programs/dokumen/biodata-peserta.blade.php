<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nominatif Peserta - {{ $program->masterProgram->name ?? '' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo blk banda.png') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
        }
        .page {
            width: 297mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 8mm 8mm 8mm 8mm;
        }

        /* ── KOP ── */
        .kop-wrap {
            display: flex;
            align-items: stretch;
            border: 1px solid #000;
            margin-bottom: 0;
        }
        .kop-logo-area {
            width: 160px;
            min-height: 75px;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px 8px;
            flex-shrink: 0;
        }
        .kop-logo-area img {
            max-width: 95px;
            max-height: 50px;
            object-fit: contain;
        }
        .kop-logo-placeholder {
            font-size: 7pt;
            color: #555;
            text-align: center;
        }
        .kop-nama-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4px 8px;
            border-right: 1px solid #000;
        }
        .kop-nama-area .lembaga-nama {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.3;
        }
        .kop-meta-area {
            width: 200px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 5px 12px;
        }
        .kop-meta-row {
            display: flex;
            font-size: 7.5pt;
            line-height: 1.6;
        }
        .kop-meta-row .k { width: 80px; }
        .kop-meta-row .v { flex: 1; }

        /* ── INFO PROGRAM ── */
        .info-program {
            border: 1px solid #000;
            border-top: none;
            padding: 4px 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px 20px;
        }
        .info-row {
            display: flex;
            font-size: 8pt;
            line-height: 1.7;
        }
        .info-row .lbl { min-width: 130px; font-weight: bold; }
        .info-row .sep { min-width: 10px; }
        .info-row .val { flex: 1; }

        /* ── TABEL NOMINATIF ── */
        .tabel-nominatif {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 0;
        }
        .tabel-nominatif th {
            background: #d0d0d0;
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            line-height: 1.3;
        }
        .tabel-nominatif td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: middle;
            line-height: 1.4;
        }
        .tabel-nominatif td.center { text-align: center; }
        .tabel-nominatif tr.empty-row td {
            color: #999;
        }

        /* ── TTD AREA ── */
        .ttd-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 0;
        }
        .ttd-table {
            border-collapse: collapse;
            font-size: 8pt;
            width: 65%;
        }
        .ttd-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: center;
            vertical-align: top;
        }
        .ttd-table .ttd-jabatan {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 3px 8px;
        }
        .ttd-table .ttd-space {
            height: 55px;
        }
        .ttd-table .ttd-nama {
            font-weight: bold;
            border-top: 1px solid #000;
            padding: 3px 8px;
        }
        .ttd-table .ttd-nip {
            font-size: 7.5pt;
            padding: 2px 8px;
        }

        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .page { margin: 0; padding: 6mm; width: 297mm; }
            @page { size: A4 landscape; margin: 0; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:10px; background:#4f46e5;">
    <button onclick="window.print()" style="padding:8px 28px; background:#fff; color:#4f46e5; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:13px;">🖨️ Cetak / Print</button>
    <button onclick="window.close(); if(!window.closed){ window.history.back(); }" style="margin-left:10px; padding:8px 20px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:13px;">← Kembali</button>
</div>

@php
    $kopBase64 = null;
    $noForm       = $settings->format_nomor  ?? 'BNA/PNY/15-36';
        $revNo        = $settings->dasar_hukum_1 ?? '00';
        $issueDate    = $settings->dasar_hukum_2 ?? '26-03-2014';
        $revisionDate = $settings->dasar_hukum_3 ?? '';
    
    if ($settings && $settings->logo_path) {
        $fullPath = storage_path('app/public/' . $settings->logo_path);
        if (file_exists($fullPath)) {
            $mime = mime_content_type($fullPath);
            $kopBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($fullPath));
        }
    }
    // Fallback ke file default
    if (!$kopBase64) {
        $logoPath = storage_path('app/public/document-logos/kop.png');
        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath);
            $kopBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    \Carbon\Carbon::setLocale('id');

    $kejuruan = $program->masterProgram?->kejuruan?->kejuruan 
             ?? $program->masterProgram?->kejuruan?->nama_kejuruan 
             ?? 'KEJURUAN BELUM DISET';

    $namaProgram   = strtoupper($program->masterProgram->name ?? $program->nama_program ?? '-');
    $waktuPelaks   = ($program->start_date ? $program->start_date->locale('id')->isoFormat('D MMMM Y') : '-') 
                   . ' s/d ' 
                   . ($program->end_date ? $program->end_date->locale('id')->isoFormat('D MMMM Y') : '-');
    $jumlahJp      = ($program->jp ?? '0') . ' JP';

    // TTD
    $pj1Nama = $settings->nama_penyusun ?? 'Oni Indah Reflin';
    $pj1Nip  = $settings->nip_penyusun ?? '19880623 202521 2 019';
    $pj2Nama = $settings->nama_pemeriksa ?? 'Andri, S.P.';
    $pj2Nip  = $settings->nip_pemeriksa ?? '19820122 200901 1 008';
    $pj3Nama = $settings->nama_pengesah ?? $settings->nama_pengirim ?? 'Rahmad Faisal';
    $pj3Nip  = $settings->nip_pengesah ?? $settings->nip_pengirim ?? '19810330 200901 1 005';

    $pesertaList = $program->participants;
    $minRows     = max(16, $pesertaList->count());
@endphp

<div class="page">

    {{-- ══ KOP SURAT ══ --}}
<div class="kop-wrap">
    <div class="kop-logo-area">
        @if($kopBase64)
            <img src="{{ $kopBase64 }}" alt="Logo">
        @else
            <div class="kop-logo-placeholder" style="font-size:7pt; color:#555; text-align:center; line-height:1.2;">
                
            </div>
        @endif
    </div>
    
    <div class="kop-nama-area" style="background: #1e3a8a; color: white; display: flex; align-items: center; justify-content: center; font-size: 11pt; font-weight: bold;">
       
    </div>
    
    <div class="kop-meta-area">
        <div class="kop-meta-row"><span class="k">No Form</span><span class="sep">:</span><span class="v">{{ $noForm }}</span></div>
<div class="kop-meta-row"><span class="k">Rev</span><span class="sep">:</span><span class="v">{{ $revNo }}</span></div>
<div class="kop-meta-row"><span class="k">Issue Date</span><span class="sep">:</span><span class="v">{{ $issueDate }}</span></div>
<div class="kop-meta-row"><span class="k">Revision Date</span><span class="sep">:</span><span class="v">{{ $revisionDate }}</span></div>
    </div>
</div>

    {{-- ══ INFO PROGRAM ══ --}}
    <div class="info-program" style="display: block; padding: 8px 10px;">
        <div class="info-row">
            <span class="lbl">KEJURUAN</span>
            <span class="sep">:</span>
            <span class="val"><strong>{{ strtoupper($kejuruan) }}</strong></span>
        </div>
        <div class="info-row">
            <span class="lbl">PROGRAM PELATIHAN</span>
            <span class="sep">:</span>
            <span class="val"><strong>{{ $namaProgram }}</strong></span>
        </div>
        <div class="info-row">
            <span class="lbl">WAKTU PELAKSANAAN</span>
            <span class="sep">:</span>
            <span class="val"><strong>{{ $waktuPelaks }}</strong></span>
        </div>
        <div class="info-row">
            <span class="lbl">JUMLAH JAM PELATIHAN</span>
            <span class="sep">:</span>
            <span class="val"><strong>{{ $jumlahJp }}</strong></span>
        </div>
    </div>

    {{-- ══ TABEL NOMINATIF ══ --}}
    <table class="tabel-nominatif" style="margin-bottom: 45px !important;">
        <thead>
            <tr>
                <th style="width:22px;">NO</th>
                <th style="width:110px;">N A M A</th>
                <th style="width:52px;">JENIS<br>KELAMIN</th>
                <th style="width:80px;">TEMPAT LAHIR</th>
                <th style="width:60px;">TGL LAHIR</th>
                <th style="width:52px;">PENDIDIKAN</th>
                <th style="width:100px;">NO KTP</th>
                <th style="width:120px;">ALAMAT</th>
                <th style="width:80px;">KABUPATEN/<br>KOTA</th>
                <th style="width:70px;">KECAMATAN</th>
                <th style="width:80px;">KELURAHAN/<br>DESA</th>
                <th style="width:72px;">NO TELP /HP</th>
                <th style="width:28px;">USIA</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i < $minRows; $i++)
                @php $p = $pesertaList[$i] ?? null; @endphp
                @if($p)
                    @php
                        $tglLahir  = $p->date_of_birth ?? $p->tanggal_lahir ?? $p->birth_date ?? null;
                        $tglFormat = $tglLahir ? \Carbon\Carbon::parse($tglLahir)->format('d/m/Y') : '-';
                        $usia      = $tglLahir ? \Carbon\Carbon::parse($tglLahir)->age : '-';
                        $jk        = $p->gender ?? $p->jenis_kelamin ?? '-';
                        $jkLabel   = (strtolower($jk) === 'laki-laki' || strtolower($jk) === 'l') ? 'LAKI-LAKI' :
                                     ((strtolower($jk) === 'perempuan' || strtolower($jk) === 'p') ? 'PEREMPUAN' : strtoupper($jk));
                                     // Pendidikan (JSON)
                        $pendidikanRaw = $p->pendidikan ?? $p->education ?? null;
                        $pendidikanVal = '-';
                        if ($pendidikanRaw) {
                            $decoded = json_decode($pendidikanRaw, true);
                            if (is_array($decoded)) {
                                $pendidikanVal = $decoded['PENDIDIKAN'] ?? $decoded['pendidikan'] ?? $pendidikanRaw;
                            } else {
                                $pendidikanVal = $pendidikanRaw;
                            }
                        }
                        // ALAMAT - SEMUA KEMUNGKINAN FIELD
            // ALAMAT - FIELD YANG BENAR
            $kabKota   = $p->asal_kabupaten ?? $p->kabupaten ?? '-';
            $kecamatan = $p->asal_kecamatan ?? $p->kecamatan ?? '-';
            $kelDesa   = $p->asal_kelurahan ?? $p->kelurahan ?? $p->desa ?? '-';
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>{{ strtoupper($p->name ?? '-') }}</td>
                        <td class="center">{{ $jkLabel }}</td>
                        <td>{{ strtoupper($p->place_of_birth ?? $p->tempat_lahir ?? $p->birth_place ?? '-') }}</td>
                        <td class="center">{{ $tglFormat }}</td>
                        <td class="center">{{ strtoupper($pendidikanVal) }}</td>
                        <td class="center">{{ $p->nik ?? $p->nip ?? '-' }}</td>
                        <td>{{ strtoupper($p->alamat ?? $p->address ?? $p->alamat_lengkap ?? '-') }}</td>
                        <td>{{ strtoupper($kabKota) }}</td>
        <td>{{ strtoupper($kecamatan) }}</td>
        <td>{{ strtoupper($kelDesa) }}</td>
                        <td class="center">{{ $p->phone ?? $p->no_hp ?? $p->telepon ?? '-' }}</td>
                        <td class="center">{{ $usia }}</td>
                    </tr>
                @else
                    <tr class="empty-row">
                        <td class="center">{{ $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="center">-</td>
                    </tr>
                @endif
            @endfor
        </tbody>
    </table>

    {{-- ══ TTD ══ --}}
    <div class="ttd-wrap">
        <table class="ttd-table">
            <tr>
                <td class="ttd-jabatan">Disiapkan Oleh,</td>
                <td class="ttd-jabatan">Diperiksa Oleh</td>
                <td class="ttd-jabatan">Disahkan Oleh,</td>
            </tr>
            <tr>
                <td class="ttd-jabatan" style="font-weight:normal; font-size:7.5pt; border-top:none;">Staff Kios Siap Kerja</td>
                <td class="ttd-jabatan" style="font-weight:normal; font-size:7.5pt; border-top:none;">Sub Koordinator Pemberdayaan</td>
                <td class="ttd-jabatan" style="font-weight:normal; font-size:7.5pt; border-top:none;">Kepala Balai</td>
            </tr>
            <tr>
                <td class="ttd-space"></td>
                <td class="ttd-space"></td>
                <td class="ttd-space"></td>
            </tr>
            <tr>
                <td class="ttd-nama">{{ $pj1Nama }}</td>
                <td class="ttd-nama">{{ $pj2Nama }}</td>
                <td class="ttd-nama">{{ $pj3Nama }}</td>
            </tr>
            <tr>
                <td class="ttd-nip">NIP. {{ $pj1Nip }}</td>
                <td class="ttd-nip">NIP. {{ $pj2Nip }}</td>
                <td class="ttd-nip">NIP. {{ $pj3Nip }}</td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
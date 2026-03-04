<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SK Penyelenggara - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
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

        /* KOP SURAT */
        .kop-wrapper {
            border-bottom: 3px solid #000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }
        .kop {
            display: flex;
            align-items: flex-start;
            margin-bottom: 4px;
        }
        .kop-logo {
            width: 90px;
            height: auto;
            margin-right: 12px;
            margin-top: 0;
            flex-shrink: 0;
        }
        .kop-logo img {
            width: 80px;
            height: auto;
            display: block;
        }
        
        .kop-text { 
            flex: 1; 
            text-align: center;
            padding-top: 0;
        }
        .kop-text .instansi { 
            font-size: 9.5pt; 
            font-weight: 600;
            text-transform: uppercase; 
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        .kop-text .dirjen { 
            font-size: 10pt; 
            font-weight: 700;
            text-transform: uppercase; 
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        .kop-text .nama-pembinaan { 
            font-size: 10pt; 
            font-weight: 700;
            text-transform: uppercase; 
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            line-height: 1.2;
        }
        .kop-text .nama-lembaga { 
            font-size: 11pt; 
            font-weight: 900;
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            margin-top: 2px;
            line-height: 1.2;
        }

        /* JUDUL */
        .judul-dok {
            text-align: center;
            margin: 14px 0 8px;
        }
        .judul-dok h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nomor-sk {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 14px;
        }

        .tentang {
            text-align: center;
            font-weight: bold;
            margin: 12px 0;
            text-transform: uppercase;
        }

        .jabatan-pembuka {
            text-align: center;
            font-weight: bold;
            margin: 12px 0;
            text-transform: uppercase;
        }

        /* Section Umum */
        .section {
            margin-bottom: 10px;
        }
        .section-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .section-label {
            display: table-cell;
            width: 140px;
            vertical-align: top;
            font-weight: bold;
        }
        .section-colon {
            display: table-cell;
            width: 20px;
            vertical-align: top;
        }
        .section-content {
            display: table-cell;
            vertical-align: top;
            text-align: justify;
        }

        /* Pasal & Poin */
        .diktum {
            font-weight: bold;
            text-transform: uppercase;
            margin: 12px 0 8px;
        }
        .pasal {
            margin: 10px 0;
        }
        .pasal-judul {
            font-weight: bold;
            text-align: center;
            margin-bottom: 6px;
        }
        .pasal-content {
    margin-left: 140px; /* Tetap sejajar dengan label KESATU/Pasal */
}

.list-item {
    margin: 4px 0;
    padding-left: 30px; /* Indent tetap untuk nomor poin */
    text-indent: 0;     /* Hilangkan indent negatif agar tidak shifting */
    text-align: justify;
    hyphens: auto;
}

/* Tambahkan ini untuk memastikan baris kedua tidak bergeser */
.list-item::before {
    content: counter(list-item) ". "; /* Otomatis nomor poin */
    counter-increment: list-item;
    position: absolute;
    left: 0;
    font-weight: normal;
}

.pasal-content {
    counter-reset: list-item; /* Reset counter per pasal */
}

        .memutuskan {
            margin: 12px 0;
            text-align: center;
            font-weight: bold;
        }

        /* TTD - Sesuai Format Template */
        .ttd-area {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            min-width: 200px;
        }
        .ttd-box .ttd-tempat {
            font-size: 11pt;
        }
        .ttd-box .ttd-tanggal {
            font-size: 11pt;
            margin-bottom: 4px;
        }
        .ttd-box .ttd-jabatan {
            font-size: 11pt;
            margin-bottom: 60px;
        }
        .ttd-box .ttd-placeholder {
            margin: 60px 0 10px;
            font-size: 10pt;
            color: #666;
        }
        .ttd-box .ttd-nama {
            font-size: 11pt;
        }
        .ttd-box .ttd-nip {
            font-size: 10pt;
        }

        /* Tabel */
        .tabel-instruktur, .tabel-peserta {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 10pt;
        }
        .tabel-instruktur th, .tabel-peserta th {
            background: #f5f5f5;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .tabel-instruktur td, .tabel-peserta td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        .tabel-instruktur td:first-child, .tabel-peserta td:first-child {
            text-align: center;
            width: 40px;
        }

        .bullet-check {
            display: block;
            margin: 3px 0;
        }
        .bullet-check::before {
            content: "▪ ";
            margin-right: 4px;
        }

        .total-row td {
            font-weight: bold;
            background: #f9f9f9;
        }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; padding: 20mm 20mm 20mm 25mm; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#be123c;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#be123c; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
        🖨️ Cetak / Print
    </button>
    <button onclick="window.close(); if(!window.closed) { window.history.back(); }" style="margin-left:12px; padding:10px 24px; background:transparent; color:#fff; border:1px solid #fff; border-radius:6px; cursor:pointer; font-size:14px;">
        ← Kembali
    </button>
</div>

<div class="page">

    <!-- KOP SURAT DENGAN GARIS -->
    <div class="kop-wrapper">
        <div class="kop">
            <div class="kop-logo">
                <img src="{{ asset('images/logo blk banda.png') }}" alt="Logo">
            </div>
            <div class="kop-text">
                <div class="instansi">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
                <div class="dirjen">DIREKTORAT JENDERAL</div>
                <div class="nama-pembinaan">PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
                <div class="nama-lembaga">BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH</div>
            </div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul-dok">
        <h2>KEPUTUSAN KEPALA BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH</h2>
    </div>
    
    @php
        $tahunAnggaran = $program->paketPelatihan->tahun ?? now()->year;
    @endphp
    
    <div class="nomor-sk">
        NOMOR ${nomor_naskah}
    </div>

    <div class="tentang">TENTANG</div>

    <p style="text-align:center; font-weight:bold; margin-bottom:12px; text-transform:uppercase;">
        PENYELENGGARAAN {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'PELATIHAN BERBASIS KOMPETENSI (PBK)' }} BATCH {{ $program->angkatan ?? '-' }} KEJURUAN {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} PROGRAM {{ $program->masterProgram->name ?? '-' }} TAHUN ANGGARAN {{ $tahunAnggaran }}
    </p>

    <div class="jabatan-pembuka">
        KEPALA BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH
    </div>

    <!-- MENIMBANG - Struktur sama seperti Menetapkan -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Menimbang</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <ol style="margin:0; padding-left:0; list-style-position:inside; text-align:justify; counter-reset:list-item;">
                <li style="margin-bottom:4px;">bahwa dalam rangka Penyiapan Tenaga Kerja yang Kompeten perlu diadakan {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }} Batch {{ $program->angkatan ?? '-' }} Kejuruan {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program {{ $program->masterProgram->name ?? '-' }} Balai Pelatihan Vokasi dan Produktivitas Banda Aceh pada Daftar Isian Pelaksanaan Anggaran (DIPA) Kementerian Ketenagakerjaan Tahun Anggaran {{ $tahunAnggaran }};</li>
                <li>bahwa untuk itu perlu dikeluarkan Surat Keputusan Kepala Balai Pelatihan Vokasi dan Produktivitas Banda Aceh tentang Penyelenggaraan {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }} Batch {{ $program->angkatan ?? '-' }} Kejuruan {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program {{ $program->masterProgram->name ?? '-' }} Tahun Anggaran {{ $tahunAnggaran }}.</li>
            </ol>
        </div>
    </div>
</div>

<!-- MENGINGAT -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Mengingat</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <ol style="margin:0; padding-left:0; list-style-position:inside; text-align:justify; counter-reset:list-item;">
                <li>Undang-Undang Nomor 13 Tahun 2003 tentang Ketenagakerjaan;</li>
                <li>Undang-Undang Nomor 17 Tahun 2003 tentang Keuangan Negara;</li>
                <li>Undang-Undang Nomor 1 Tahun 2004 tentang Perbendaharaan Negara;</li>
                <li>Undang-Undang Nomor 15 Tahun 2004 tentang Pemeriksaan Pengelolaan dan Tanggung Jawab Keuangan Negara;</li>
                <li>Peraturan Pemerintah Nomor 45 Tahun 2013 tentang Tata Cara Pelaksanaan Anggaran Pendapatan dan Belanja Negara (Lembaran Negara Republik Indonesia Tahun 2013 Nomor 103);</li>
                <li>Peraturan Menteri Keuangan Nomor 217/PMK.05/2022 tentang Sistem Akuntansi dan Pelaporan Keuangan Pemerintah Pusat;</li>
                <li>Peraturan Menteri Keuangan Nomor 210/PMK.05/2022 tentang Tata Cara Pembayaran Dalam Rangka Pelaksanaan Anggaran Pendapatan dan Belanja Negara;</li>
                <li>Peraturan Menteri Ketenagakerjaan Nomor 8 Tahun 2014 tentang Pedoman Penyelenggaraan Pelatihan Berbasis Kompetensi;</li>
                <li>Peraturan Menteri Ketenagakerjaan Nomor 1 tahun 2022 tentang Organisasi dan Tata Kerja Unit Pelaksana Teknis di Kementerian Ketenagakerjaan;</li>
                <li>Peraturan Menteri Ketenagakerjaan Nomor 1 Tahun 2021 tentang Organisasi dan Tata Kerja Kementerian Ketenagakerjaan.</li>
            </ol>
        </div>
    </div>
</div>

<!-- HALAMAN BARU -->
<div style="page-break-before: always;"></div>

<!-- MEMPERHATIKAN -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Memperhatikan</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <ol style="margin:0; padding-left:0; list-style-position:inside; text-align:justify; counter-reset:list-item;">
                <li>Surat Pengesahan Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun {{ $tahunAnggaran }} Nomor : 026.13.2.065106/{{ $tahunAnggaran - 1 }} tanggal 28 November {{ $tahunAnggaran - 1 }};</li>
                <li>Keputusan Menteri Ketenagakerjaan Nomor 195 Tahun 2021 tentang Pengangkatan Pejabat Perbendaharaan Negara pada Satuan Kerja Pusat dan Satuan Kerja Unit Pelaksana Teknis Pusat Kementerian Ketenagakerjaan.</li>
            </ol>
        </div>
    </div>
</div>

<div class="memutuskan">MEMUTUSKAN :</div>

<!-- Menetapkan -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Menetapkan</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <strong>KEPUTUSAN KEPALA BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH TENTANG PENYELENGGARAAN {{ strtoupper($program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'PELATIHAN BERBASIS KOMPETENSI (PBK)') }} BATCH {{ $program->angkatan ?? '-' }} KEJURUAN {{ strtoupper($program->masterProgram->kejuruan->nama_kejuruan ?? '-') }} PROGRAM {{ strtoupper($program->masterProgram->name ?? '-') }} TAHUN ANGGARAN {{ $tahunAnggaran }}.</strong>
        </div>
    </div>
</div>

<!-- KESATU -->
<div class="section">
    <div class="section-row">
        <div class="section-label">KESATU</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            Menyelenggarakan {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }} Batch {{ $program->angkatan ?? '-' }} Kejuruan {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program {{ $program->masterProgram->name ?? '-' }} Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun Anggaran {{ $tahunAnggaran }}, dengan ketentuan sebagaimana disebutkan pada pasal-pasal tersebut dibawah ini :
        </div>
    </div>
</div>

<!-- Pasal 1 -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Pasal 1</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <ol style="margin:0; padding-left:0; list-style-position:inside; text-align:justify;">
                <li>Pelatihan diselenggarakan mulai tanggal <strong>{{ $program->start_date ? $program->start_date->format('d F Y') : '-' }}</strong> sampai dengan <strong>{{ $program->end_date ? $program->end_date->format('d F Y') : '-' }}</strong> dengan jumlah jam pelatihan sebanyak <strong>{{ $program->jp ?? '-' }} @45 menit</strong> dengan jumlah peserta sebanyak <strong>{{ $program->participants->count() }} orang</strong> (daftar peserta terlampir);</li>
                <li>Lokasi {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }} Batch {{ $program->angkatan ?? '-' }} Kejuruan {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program {{ $program->masterProgram->name ?? '-' }} bertempat di Balai Pelatihan Vokasi dan Produktivitas Banda Aceh, Jalan Kesatria Desa Geuceu Komplek, Kota Banda Aceh, Provinsi Aceh;</li>
                <li>Pengajar/Instruktur yang berstatus Pegawai Negeri Sipil di Balai Pelatihan Vokasi dan Produktivitas Banda Aceh untuk Pengajar Outsourcing sebesar Rp. 40.000,- / JP, Pengajar Soft Skill dan Pengajar dari Industri diberikan honorarium sebesar Rp. 100.000,- / JP.</li>
            </ol>
        </div>
    </div>
</div>

<!-- Pasal 2 -->
<div class="section">
    <div class="section-row">
        <div class="section-label">Pasal 2</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            <ol style="margin:0; padding-left:0; list-style-position:inside; text-align:justify;">
                @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
                <li>Menunjuk Saudara <strong>{{ $pj->instructor->name ?? '-' }}</strong> sebagai Penanggung Jawab Kelas;</li>
                <li>Unit kompetensi teori/praktek, jumlah jam pelatihan dan nama Pengajar/Instruktur sebagaimana terlampir.</li>
            </ol>
        </div>
    </div>
</div>

<!-- KEDUA -->
<div class="section">
    <div class="section-row">
        <div class="section-label">KEDUA</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            Segala biaya yang timbul akibat dikeluarkannya Keputusan ini dibebankan pada Surat Pengesahan Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Nomor : 026.13.2.065106/{{ $tahunAnggaran - 1 }} tanggal 28 November {{ $tahunAnggaran - 1 }}.
        </div>
    </div>
</div>

<!-- KETIGA -->
<div class="section">
    <div class="section-row">
        <div class="section-label">KETIGA</div>
        <div class="section-colon">:</div>
        <div class="section-content">
            Keputusan ini berlaku sejak tanggal ditetapkan, dengan ketentuan apabila dikemudian hari terdapat kekeliruan dalam keputusan ini akan diperbaiki sebagaimana mestinya.
        </div>
    </div>
</div>

    <!-- TTD - TEMPLATE VARIABLE -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div>Ditetapkan di Banda Aceh</div>
            <div>Pada tanggal $&#123;tanggal_naskah&#125;</div>
            <div style="margin-top:8px;">Kepala Balai,</div>
            <div style="margin:60px 0 10px;">$&#123;ttd_pengirim&#125;</div>
            <div>$&#123;nama_pengirim&#125;</div>
            <div>NIP $&#123;nip_pengirim&#125;</div>
        </div>
    </div>

    <!-- ===== LAMPIRAN I: INSTRUKTUR ===== -->
    <div style="page-break-before: always; padding-top: 10mm;">
        <p style="text-align:right; margin-bottom:4px; font-size:10pt; font-weight:bold;">LAMPIRAN I</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">KEPUTUSAN KEPALA BALAI PELATIHAN VOKASI</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">DAN PRODUKTIVITAS BANDA ACEH.</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt; font-weight:bold;">NOMOR $&#123;nomor_naskah&#125;</p>
        <p style="text-align:right; margin-bottom:14px; font-size:9.5pt;">TENTANG PENYELENGGARAAN {{ strtoupper($program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '') }} BATCH {{ $program->angkatan ?? '-' }} KEJURUAN {{ strtoupper($program->masterProgram->kejuruan->nama_kejuruan ?? '') }} PROGRAM {{ strtoupper($program->masterProgram->name ?? '') }} TAHUN ANGGARAN {{ $tahunAnggaran }}.</p>

        <p style="text-align:center; font-weight:bold; text-transform:uppercase; margin-bottom:14px; font-size:11pt;">
            SUSUNAN INSTRUKTUR DAN MATERI KEGIATAN
        </p>

        @php
            $unitsData = $program->selected_units_with_details ?? collect([]);
            $instructors = $program->programInstructors;
        @endphp

        <table class="tabel-instruktur">
            <thead>
                <tr>
                    <th style="width:40px;">NO</th>
                    <th>NAMA<br>INSTRUKTUR</th>
                    <th style="width:100px;">INSTRUKTUR<br>PENGGANTI</th>
                    <th>UNIT KOMPETENSI</th>
                    <th style="width:80px;">LAMA<br>LATIHAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($instructors as $idx => $instructor)
                    @php
                        $totalUnits = $unitsData->count();
                        $rowspan = $totalUnits > 0 ? $totalUnits : 1;
                    @endphp
                    
                    <tr>
                        <td rowspan="{{ $rowspan }}">{{ $idx + 1 }}.</td>
                        <td rowspan="{{ $rowspan }}">{{ $instructor->instructor->name ?? '-' }}</td>
                        <td rowspan="{{ $rowspan }}"></td>
                        
                        @if($totalUnits > 0)
                            <td><span class="bullet-check">{{ $unitsData->first()['unit']->name ?? '-' }}</span></td>
                            <td style="text-align:center;">{{ $unitsData->first()['custom_duration'] ?? 0 }} JP</td>
                        @else
                            <td>-</td>
                            <td>-</td>
                        @endif
                    </tr>

                    @foreach($unitsData->slice(1) as $unit)
                    <tr>
                        <td><span class="bullet-check">{{ $unit['unit']->name ?? '-' }}</span></td>
                        <td style="text-align:center;">{{ $unit['custom_duration'] ?? 0 }} JP</td>
                    </tr>
                    @endforeach

                @empty
                    <tr>
                        <td>1.</td>
                        <td>-</td>
                        <td></td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                @endforelse

                <tr class="total-row">
                    <td colspan="4" style="text-align:right; padding-right:12px;">JUMLAH</td>
                    <td style="text-align:center;">{{ $program->jp ?? 0 }} JP</td>
                </tr>
            </tbody>
        </table>

        <div class="ttd-area" style="margin-top:24px;">
            <div class="ttd-box">
                <div>Kepala Balai,</div>
                <div style="margin:60px 0 10px;">$&#123;ttd_pengirim&#125;</div>
                <div>$&#123;nama_pengirim&#125;</div>
                <div>NIP $&#123;nip_pengirim&#125;</div>
            </div>
        </div>
    </div>

    <!-- ===== LAMPIRAN II: PESERTA ===== -->
    <div style="page-break-before: always; padding-top: 10mm;">
        <p style="text-align:right; margin-bottom:4px; font-size:10pt; font-weight:bold;">LAMPIRAN II</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">KEPUTUSAN KEPALA BALAI PELATIHAN VOKASI</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">DAN PRODUKTIVITAS BANDA ACEH.</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt; font-weight:bold;">NOMOR $&#123;nomor_naskah&#125;</p>
        <p style="text-align:right; margin-bottom:14px; font-size:9.5pt;">TENTANG PENYELENGGARAAN {{ strtoupper($program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? '') }} BATCH {{ $program->angkatan ?? '-' }} KEJURUAN {{ strtoupper($program->masterProgram->kejuruan->nama_kejuruan ?? '') }} PROGRAM {{ strtoupper($program->masterProgram->name ?? '') }} TAHUN ANGGARAN {{ $tahunAnggaran }}.</p>

        <p style="text-align:center; font-weight:bold; text-transform:uppercase; margin-bottom:14px; font-size:11pt;">
            SUSUNAN PESERTA PELATIHAN
        </p>

        <table class="tabel-peserta">
            <thead>
                <tr>
                    <th style="width:40px;">NO</th>
                    <th>NAMA</th>
                    <th style="width:140px;">TEMPAT & TANGGAL LAHIR</th>
                    <th style="width:90px;">JENIS<br>KELAMIN</th>
                    <th style="width:80px;">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($program->participants as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->name ?? '-' }}</td>
                    <td style="font-size:9.5pt;">
                        @if(isset($p->tempat_lahir) || isset($p->tanggal_lahir))
                            {{ $p->tempat_lahir ?? '-' }} {{ isset($p->tanggal_lahir) ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $p->gender ?? $p->jenis_kelamin ?? '-' }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#888; padding:16px;">Belum ada peserta</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd-area" style="margin-top:24px;">
            <div class="ttd-box">
                <div>Kepala Balai,</div>
                <div style="margin:60px 0 10px;">$&#123;ttd_pengirim&#125;</div>
                <div>$&#123;nama_pengirim&#125;</div>
                <div>NIP $&#123;nip_pengirim&#125;</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
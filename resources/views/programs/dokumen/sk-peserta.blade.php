<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tugas Peserta - {{ $program->masterProgram->name ?? '' }}</title>
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
        .kop {
            display: flex;
            align-items: flex-start;
            border-bottom: 3px solid #000;
            padding-bottom: 8px;
            margin-bottom: 3px;
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

        .alamat-detail {
            text-align: center;
            font-size: 8pt;
            margin-top: 3px;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        /* JUDUL */
        .judul-dok {
            text-align: center;
            margin: 16px 0 6px;
        }
        .judul-dok h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .nomor-st {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 14px;
        }

        /* Section Menimbang, Dasar, dll */
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

        /* List items */
        .list-item {
            margin: 3px 0;
            padding-left: 24px;
            text-indent: -24px;
        }

        /* Memerintahkan section */
        .memerintahkan {
            margin: 12px 0;
            text-align: center;
            font-weight: bold;
        }

        /* TTD */
        .ttd-area {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            text-align: center;
            min-width: 200px;
        }
        .ttd-box .ttd-tempat-tgl {
            margin-bottom: 4px;
            font-size: 11pt;
        }
        .ttd-box .ttd-jabatan {
            margin-bottom: 4px;
            font-size: 11pt;
        }
        .ttd-box .ttd-qr {
            margin: 50px auto 10px;
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ttd-box .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 11pt;
        }
        .ttd-box .ttd-nip {
            font-size: 10pt;
        }

        /* Tabel Peserta */
        .tabel-peserta {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 10pt;
        }
        .tabel-peserta th {
            background: #f5f5f5;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .tabel-peserta td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        .tabel-peserta td:first-child {
            text-align: center;
            width: 40px;
        }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; padding: 20mm 20mm 20mm 25mm; }
        }
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

<div class="page">

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">
            <img src="{{ asset('images/logo blk banda.png') }}" alt="Logo">
        </div>
        <div class="kop-text">
            <div class="instansi">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
            <div class="dirjen">DIREKTORAT JENDERAL</div>
            <div class="nama-pembinaan">PEMBINAAN PELATIHAN VOKASI DAN PRODUKTIVITAS</div>
            <div class="nama-lembaga">BALAI PELATIHAN VOKASI DAN PRODUKTIVITAS BANDA ACEH</div>
            <!-- Alamat Detail -->
            <div class="alamat-detail">
                Jalan Kesatria, Geuceu Komp. Banda Raya, Kota Banda Aceh 23239, Telepon (0651) 45298<br>
                Laman : http://www.kemnaker.go.id Email : blkbandaaceh@kemnaker.go.id
            </div>
        </div>
    </div>


    <!-- JUDUL -->
    <div class="judul-dok">
        <h2>SURAT TUGAS</h2>
    </div>
    <div class="nomor-st">
        NOMOR {{ now()->format('j.n') }}/___/LP.00.04/{{ strtoupper(\Carbon\Carbon::parse(now())->locale('id')->translatedFormat('M')) }}/{{ now()->year }}
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
                <div class="list-item">1. Peraturan Menteri Ketenagakerjaan Nomor 8 Tahun 2014 tentang Pedoman Penyelenggaraan Pelatihan Berbasis Kompetensi;</div>
                <div class="list-item">2. Keputusan Ketenagakerjaan Republik Indonesia Nomor 248 Tahun 2023 tentang Pengangkatan Pejabat Perbendaharaan Negara pada Satuan Kerja Pusat dan Satuan Kerja Unit Pelaksana Teknis Pusat dan Kementerian Ketenagakerjaan;</div>
                <div class="list-item">3. Peraturan Menteri Ketenagakerjaan Nomor 1 Tahun 2022 tentang Organisasi dan Tata Kerja Unit Pelaksana Teknis di Kementerian Ketenagakerjaan;</div>
                <div class="list-item">4. Surat Keputusan Kepala Balai Pelatihan Vokasi dan Produktivitas Nomor <strong>___/___/{{ now()->year }}</strong> tanggal <strong>{{ now()->format('d F Y') }}</strong> tentang Penyelenggaraan {{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan Berbasis Kompetensi (PBK)' }} Batch {{ $program->angkatan ?? '-' }} Kejuruan {{ $program->masterProgram->kejuruan->nama_kejuruan ?? '-' }} Program {{ $program->masterProgram->name ?? '-' }} Tahun Anggaran {{ now()->year }};</div>
                <div class="list-item">5. Daftar Isian Pelaksanaan Anggaran (SP-DIPA) Balai Pelatihan Vokasi dan Produktivitas Banda Aceh Tahun {{ now()->year }} Nomor : 026.13.2.065106/{{ now()->year - 1 }} tanggal 28 November {{ now()->year - 1 }}.</div>
            </div>
        </div>
    </div>

    <!-- MEMERINTAHKAN -->
    <div class="memerintahkan">Memerintahkan :</div>

    <!-- KEPADA -->
    <div class="section">
        <div class="section-row">
            <div class="section-label">Kepada</div>
            <div class="section-colon">:</div>
            <div class="section-content">Nama-nama peserta terlampir.</div>
        </div>
    </div>

    <!-- UNTUK -->
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
            <div class="ttd-tempat-tgl">Banda Aceh, {{ now()->format('d F Y') }}</div>
            <div class="ttd-jabatan">Kepala Balai,</div>
            
            <!-- QR Code -->
            <div class="ttd-qr">
                @if(function_exists('QrCode'))
                    {!! QrCode::size(100)->generate(route('admin.programs.dokumen.spt-peserta', $program)) !!}
                @else
                    <svg width="100" height="100" viewBox="0 0 100 100">
                        <rect width="100" height="100" fill="white"/>
                        <rect x="10" y="10" width="10" height="10" fill="black"/>
                        <rect x="30" y="10" width="10" height="10" fill="black"/>
                        <rect x="50" y="10" width="10" height="10" fill="black"/>
                        <rect x="70" y="10" width="10" height="10" fill="black"/>
                        <rect x="10" y="30" width="10" height="10" fill="black"/>
                        <rect x="50" y="30" width="10" height="10" fill="black"/>
                        <rect x="70" y="30" width="10" height="10" fill="black"/>
                        <rect x="10" y="50" width="10" height="10" fill="black"/>
                        <rect x="30" y="50" width="10" height="10" fill="black"/>
                        <rect x="70" y="50" width="10" height="10" fill="black"/>
                        <rect x="10" y="70" width="10" height="10" fill="black"/>
                        <rect x="30" y="70" width="10" height="10" fill="black"/>
                        <rect x="50" y="70" width="10" height="10" fill="black"/>
                        <rect x="70" y="70" width="10" height="10" fill="black"/>
                    </svg>
                @endif
            </div>
            
            <div class="ttd-nama">Rahmad Faisal</div>
            <div class="ttd-nip">NIP 198103302009011005</div>
        </div>
    </div>

    <!-- ===== HALAMAN LAMPIRAN ===== -->
    <div style="page-break-before: always; padding-top: 10mm;">
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">Lampiran Surat Perintah Peserta</p>
        <p style="text-align:right; margin-bottom:4px; font-size:10pt;">Nomor : {{ now()->format('j.n') }}/___/LP.00.04/{{ strtoupper(\Carbon\Carbon::parse(now())->locale('id')->translatedFormat('M')) }}/{{ now()->year }}</p>
        <p style="text-align:right; margin-bottom:16px; font-size:10pt;">Tanggal : {{ now()->format('d F Y') }}</p>

        <p style="text-align:center; font-weight:bold; text-transform:uppercase; margin-bottom:14px; font-size:11pt;">
            DAFTAR NAMA PESERTA
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
                            {{ $p->tempat_lahir ?? '-' }} {{ isset($p->tanggal_lahir) ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d F Y') : '' }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align:center;">{{ $p->gender ?? $p->jenis_kelamin ?? '-' }}</td>
                    <td></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#888; padding:16px;">Belum ada peserta terdaftar</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="ttd-area" style="margin-top:24px;">
            <div class="ttd-box">
                <div class="ttd-jabatan">Kepala Balai,</div>
                
                <div class="ttd-qr">
                    @if(function_exists('QrCode'))
                        {!! QrCode::size(100)->generate(route('admin.programs.dokumen.spt-peserta', $program)) !!}
                    @else
                        <svg width="100" height="100" viewBox="0 0 100 100">
                            <rect width="100" height="100" fill="white"/>
                            <rect x="10" y="10" width="10" height="10" fill="black"/>
                            <rect x="30" y="10" width="10" height="10" fill="black"/>
                            <rect x="50" y="10" width="10" height="10" fill="black"/>
                            <rect x="70" y="10" width="10" height="10" fill="black"/>
                            <rect x="10" y="30" width="10" height="10" fill="black"/>
                            <rect x="50" y="30" width="10" height="10" fill="black"/>
                            <rect x="70" y="30" width="10" height="10" fill="black"/>
                            <rect x="10" y="50" width="10" height="10" fill="black"/>
                            <rect x="30" y="50" width="10" height="10" fill="black"/>
                            <rect x="70" y="50" width="10" height="10" fill="black"/>
                            <rect x="10" y="70" width="10" height="10" fill="black"/>
                            <rect x="30" y="70" width="10" height="10" fill="black"/>
                            <rect x="50" y="70" width="10" height="10" fill="black"/>
                            <rect x="70" y="70" width="10" height="10" fill="black"/>
                        </svg>
                    @endif
                </div>
                
                <div class="ttd-nama">Rahmad Faisal</div>
                <div class="ttd-nip">NIP 198103302009011005</div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Penugasan Instruktur - {{ $program->masterProgram->name ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background: #fff;
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

        /* Info detail alamat */
        .alamat-detail {
            text-align: center;
            font-size: 8pt;
            margin-top: 3px;
            margin-bottom: 8px;
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

        /* Paragraf pembuka */
        .paragraf-pembuka {
            text-align: justify;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        /* Tabel Unit Kompetensi */
        .tabel-unit {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 10.5pt;
        }
        .tabel-unit th {
            background: #f5f5f5;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        .tabel-unit td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        .tabel-unit td:first-child {
            text-align: center;
            width: 40px;
        }
        .tabel-unit td:nth-child(3) {
            text-align: center;
            width: 100px;
        }
        .tabel-unit td:last-child {
            text-align: center;
            width: 70px;
        }

        /* Bullet unicode */
        .bullet-check {
            display: block;
            margin: 3px 0;
        }
        .bullet-check::before {
            content: "❖ ";
            margin-right: 4px;
        }

        /* Row total */
        .total-row td {
            font-weight: bold;
            background: #f9f9f9;
        }

        /* Paragraf penutup */
        .paragraf-penutup {
            text-align: justify;
            line-height: 1.6;
            margin-top: 12px;
            margin-bottom: 20px;
        }

        /* TTD Area */
        .ttd-area {
            margin-top: 20px;
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

        /* Footer elektronik */
        .footer-elektronik {
            position: fixed;
            bottom: 8mm;
            left: 25mm;
            right: 20mm;
            border-top: 1px solid #000;
            padding-top: 4px;
            text-align: center;
            font-size: 7pt;
            line-height: 1.3;
        }

        @media print {
            .no-print { display: none !important; }
            .page { margin: 0; padding: 20mm 20mm 20mm 25mm; }
            .footer-elektronik { display: block !important; }
        }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center; padding:12px; background:#7c3aed;">
    <button onclick="window.print()" style="padding:10px 32px; background:#fff; color:#7c3aed; font-weight:bold; border:none; border-radius:6px; cursor:pointer; font-size:14px;">
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
        </div>
    </div>

    <!-- Alamat Detail -->
    {{-- <div class="alamat-detail">
        Jalan Kesatria Geuceu Komplek Banda Raya, Kota Banda Aceh 23239, Telepon (0651) 45298<br>
        Laman : http://www.kemnaker.go.id Email: blkbandaaceh@kemnaker.go.id
    </div> --}}

    <!-- JUDUL -->
    <div class="judul-dok">
        <h2>SURAT PENUGASAN INSTRUKTUR</h2>
    </div>
    <div class="nomor-st">
        Nomor {{ now()->format('j.n') }}/___/LP.00.04/{{ strtoupper(\Carbon\Carbon::parse(now())->locale('id')->translatedFormat('M')) }}/{{ now()->year }}
    </div>

    <!-- PARAGRAF PEMBUKA -->
    <div class="paragraf-pembuka">
        <p>
            Kepala Balai Pelatihan Vokasi dan Produktivitas Banda Aceh dengan ini menginstruksikan Saudara/i yang tersebut namanya di bawah ini untuk melaksanakan 
            <strong>{{ $program->paketPelatihan->jenisPelatihan->jenis_pelatihan ?? 'Pelatihan' }} {{ $program->masterProgram->name ?? '-' }}</strong> 
            Angkatan <strong>{{ $program->angkatan ?? '-' }}</strong> selama <strong>{{ $program->jp ?? '-' }} JP</strong>. 
            Dimulai tanggal <strong>{{ $program->start_date ? $program->start_date->format('d F Y') : '-' }}</strong> sampai dengan 
            <strong>{{ $program->end_date ? $program->end_date->format('d F Y') : '-' }}</strong> bertempat di BPVP Banda Aceh dengan menunjuk Saudara/i 
            @php $pj = $program->programInstructors->where('is_penanggung_jawab', true)->first(); @endphp
            <strong>{{ $pj->instructor->name ?? '-' }}</strong>, sebagai Instruktur dan Wali Kelas 
            <strong>{{ $program->masterProgram->name ?? '-' }} {{ $program->angkatan ?? '-' }}</strong>.
        </p>
    </div>

    <!-- TABEL UNIT KOMPETENSI -->
    @php
        $unitsData = $program->selected_units_with_details ?? collect([]);
        $instructors = $program->programInstructors;
    @endphp

    <table class="tabel-unit">
        <thead>
            <tr>
                <th style="width:40px;">NO.</th>
                <th>NAMA<br>INSTRUKTUR</th>
                <th style="width:100px;">INSTRUKTUR<br>PENGGANTI</th>
                <th>JUDUL UNIT KOMPETENSI</th>
                <th style="width:70px;">DURASI</th>
            </tr>
        </thead>
        <tbody>
            @forelse($instructors as $idx => $instructor)
                @php
                    $totalUnits = $unitsData->count();
                    $rowspan = $totalUnits > 0 ? $totalUnits : 1;
                @endphp
                
                {{-- Baris pertama dengan rowspan --}}
                <tr>
                    <td rowspan="{{ $rowspan }}">{{ $idx + 1 }}.</td>
                    <td rowspan="{{ $rowspan }}">
                        {{ $instructor->instructor->name ?? '-' }}
                    </td>
                    <td rowspan="{{ $rowspan }}"></td>
                    
                    @if($totalUnits > 0)
                        <td>
                            <span class="bullet-check">{{ $unitsData->first()['unit']->name ?? '-' }}</span>
                        </td>
                        <td>{{ $unitsData->first()['custom_duration'] ?? 0 }} JP</td>
                    @else
                        <td>-</td>
                        <td>-</td>
                    @endif
                </tr>

                {{-- Baris unit berikutnya (tanpa NO, NAMA, PENGGANTI karena sudah rowspan) --}}
                @foreach($unitsData->slice(1) as $unit)
                <tr>
                    <td>
                        <span class="bullet-check">{{ $unit['unit']->name ?? '-' }}</span>
                    </td>
                    <td>{{ $unit['custom_duration'] ?? 0 }} JP</td>
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

            {{-- TOTAL ROW - Merge 4 kolom pertama --}}
            <tr class="total-row">
                <td colspan="4" style="text-align:center; padding-center:12px;">JUMLAH</td>
                <td>{{ $program->jp ?? 0 }} JP</td>
            </tr>
        </tbody>
    </table>

    <!-- PARAGRAF PENUTUP -->
    <div class="paragraf-penutup">
        <p>Demikian Instruksi ini harap dilaksanakan sebagaimana mestinya.</p>
    </div>

    <!-- TTD -->
    <div class="ttd-area">
        <div class="ttd-box">
            <div class="ttd-tempat-tgl">Banda Aceh, {{ now()->format('d F Y') }}</div>
            <div class="ttd-jabatan">Kepala Balai,</div>
            
            <!-- QR Code -->
            <div class="ttd-qr">
                @if(function_exists('QrCode'))
                    {!! QrCode::size(100)->generate(route('admin.programs.dokumen.st-instruktur', $program)) !!}
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
            
            {{-- NAMA KEPALA BALAI --}}
            <div class="ttd-nama">Rahmad Faisal</div>
            <div class="ttd-nip">NIP 198103302009011005</div>
        </div>
    </div>

</div>

<!-- FOOTER ELEKTRONIK -->
<div class="footer-elektronik">
    Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik<br>
    yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).
</div>

</body>
</html>
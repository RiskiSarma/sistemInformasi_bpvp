<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DAFTAR UNIT KOMPETENSI</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #000;
        }
        .container {
            width: 297mm;
            height: 210mm;
            position: relative;
            padding: 25mm 30mm 20mm 30mm; /* lebih banyak padding atas/bawah */
            box-sizing: border-box;
            background: #fff; /* latar putih polos, hilangkan background image agar teks jelas */
        }
        h1 {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            margin: 0 0 12mm 0;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.8pt;
            margin-top: 5mm;
        }
        th, td {
            border: 1px solid #333;
            padding: 3mm 3mm;
            vertical-align: middle;
            line-height: 1.4;
        }
        th {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: center;
        }
        .no { width: 15mm; text-align: center; }
        .unit { width: 130mm; text-align: left; padding-left: 4mm; }
        .kode { width: 55mm; text-align: center; }
        .status { width: 65mm; text-align: center; font-weight: bold; color: #006400; }
        .footer {
            position: absolute;
            bottom: 12mm;
            left: 30mm;
            right: 30mm;
            text-align: center;
            font-size: 6.8pt;
            line-height: 1.4;
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>DAFTAR UNIT KOMPETENSI</h1>

        <table>
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th class="unit">Unit Kompetensi</th>
                    <th class="kode">Kode Unit</th>
                    <th class="status">Lulus / Belum Lulus</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($units as $index => $unit)
                    <tr>
                        <td class="no">{{ $index + 1 }}</td>
                        <td class="unit">{{ $unit->name ?? '-' }}</td>
                        <td class="kode">{{ $unit->code ?: '-' }}</td>
                        <td class="status">LULUS</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:15mm 0; font-style:italic; font-size:11pt;">
                            Tidak ada unit kompetensi terdaftar untuk program ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik<br>
            yang diterbitkan oleh Balai Besar Sertifikasi Elektronik (BSrE), Badan Siber dan Sandi Negara (BSSN).
        </div>
    </div>
</body>
</html>
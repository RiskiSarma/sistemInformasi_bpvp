<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ ucfirst($reportType) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN {{ strtoupper($reportType) }}</h2>
        <p>Tanggal Cetak: {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($reportType === 'program')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Program</th>
                    <th>Batch</th>
                    <th>Deskripsi</th>
                    <th>Durasi (Jam)</th>
                    <th>Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $program)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $program->masterProgram->code ?? '-' }}</td>
                    <td>{{ $program->masterProgram->name ?? '-' }}</td>
                    <td>{{ $program->batch }}</td>
                    <td>{{ $program->masterProgram ? Str::limit($program->masterProgram->description, 100) : '-' }}</td>
                    <td>{{ $program->masterProgram->duration_hours ?? '-' }} jam</td>
                    <td>{{ $program->participants->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($reportType === 'participant')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Batch</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->name ?? '-' }}</td>
                    <td>{{ $participant->email ?? '-' }}</td>
                    <td>{{ $participant->program->masterProgram->name ?? '-' }}</td>
                    <td>{{ $participant->program->batch ?? '-' }}</td>
                    <td>{{ ucfirst($participant->status ?? '-') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($reportType === 'attendance')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Program</th>
                    <th>Batch</th>
                    <th>Peserta</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->date ? date('d/m/Y', strtotime($attendance->date)) : '-' }}</td>
                    <td>{{ $attendance->program->masterProgram->name ?? '-' }}</td>
                    <td>{{ $attendance->program->batch ?? '-' }}</td>
                    <td>{{ $attendance->participant->name ?? '-' }}</td>
                    <td>{{ ucfirst($attendance->status ?? '-') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($reportType === 'certificate')
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Program</th>
                    <th>Batch</th>
                    <th>Tanggal Lulus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $participant)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $participant->name ?? '-' }}</td>
                    <td>{{ $participant->program->masterProgram->name ?? '-' }}</td>
                    <td>{{ $participant->program->batch ?? '-' }}</td>
                    <td>{{ $participant->updated_at ? date('d/m/Y', strtotime($participant->updated_at)) : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Total Data: {{ $data->count() }}</p>
    </div>
</body>
</html>
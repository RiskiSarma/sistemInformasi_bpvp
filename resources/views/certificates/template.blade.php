<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
        }
        .certificate {
            width: 297mm;
            height: 210mm;
            position: relative;
            background-image: url('{{ public_path("images/certificate-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .content {
            position: absolute;
            width: 100%;
            text-align: center;
        }
        
        /* SESUAIKAN POSISI INI SESUAI DESAIN ANDA */
        .certificate-number {
            position: absolute;
            top: 30mm;
            right: 30mm;
            font-size: 11px;
            color: #333;
        }
        
        .participant-name {
            position: absolute;
            top: 95mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #2c5f6f;
            text-transform: uppercase;
        }
        
        .program-name {
            position: absolute;
            top: 120mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #2c5f6f;
        }
        
        .program-batch {
            position: absolute;
            top: 130mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
            color: #333;
        }
        
        .issue-date {
            position: absolute;
            top: 160mm;
            right: 70mm;
            font-size: 12px;
            color: #333;
        }
        
        .signature-left {
            position: absolute;
            bottom: 30mm;
            left: 50mm;
            text-align: center;
            font-size: 12px;
        }
        
        .signature-right {
            position: absolute;
            bottom: 30mm;
            right: 50mm;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Certificate Number -->
        <div class="certificate-number">
            No. {{ $certificate->certificate_number }}
        </div>
        
        <!-- Participant Name -->
        <div class="participant-name">
            {{ strtoupper($certificate->participant->name) }}
        </div>
        
        <!-- Program Name -->
        <div class="program-name">
            {{ $certificate->program->masterProgram->name ?? 'N/A' }}
        </div>
        
        <!-- Program Batch -->
        <div class="program-batch">
            {{ $certificate->program->batch ?? '' }}
        </div>
        
        <!-- Issue Date -->
        <div class="issue-date">
            Banda Aceh, {{ \Carbon\Carbon::parse($certificate->issue_date)->isoFormat('D MMMM Y') }}
        </div>
        
        <!-- Signature Left -->
        <div class="signature-left">
            <div style="margin-top: 50px;">_______________________</div>
            <div style="font-weight: bold; margin-top: 5px;">Kepala BPVP Banda Aceh</div>
            <div style="font-size: 11px;">NIP. __________________</div>
        </div>
        
        <!-- Signature Right -->
        <div class="signature-right">
            <div style="margin-top: 50px;">_______________________</div>
            <div style="font-weight: bold; margin-top: 5px;">Instruktur</div>
            <div style="font-size: 11px;">{{ $certificate->program->instructor->name ?? 'Instruktur' }}</div>
        </div>
    </div>
</body>
</html>
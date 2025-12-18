<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $data;
    protected $reportType;

    public function __construct($data, $reportType)
    {
        $this->data = $data;
        $this->reportType = $reportType;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        switch ($this->reportType) {
            case 'program':
                return ['No', 'Nama Program', 'Batch', 'Deskripsi', 'Durasi (Jam)', 'Jumlah Peserta'];
            case 'participant':
                return ['No', 'Nama', 'Email', 'Program', 'Batch', 'Status'];
            case 'attendance':
                return ['No', 'Tanggal', 'Program', 'Batch', 'Peserta', 'Status'];
            case 'certificate':
                return ['No', 'Nama', 'Program', 'Batch', 'Tanggal Lulus'];
            default:
                return [];
        }
    }

    public function map($row): array
    {
        static $index = 0;
        $index++;

        switch ($this->reportType) {
            case 'program':
                return [
                    $index,
                    $row->name,
                    $row->batch,
                    $row->description,
                    $row->duration,
                    $row->participants->count()
                ];
            case 'participant':
                return [
                    $index,
                    $row->name ?? '-',
                    $row->email ?? '-',
                    $row->program->name ?? '-',
                    $row->program->batch ?? '-',
                    ucfirst($row->status ?? '-')
                ];
            case 'attendance':
                return [
                    $index,
                    $row->date ? date('d/m/Y', strtotime($row->date)) : '-',
                    $row->program->name ?? '-',
                    $row->program->batch ?? '-',
                    $row->participant->name ?? '-',
                    ucfirst($row->status ?? '-')
                ];
            case 'certificate':
                return [
                    $index,
                    $row->name ?? '-',
                    $row->program->name ?? '-',
                    $row->program->batch ?? '-',
                    $row->updated_at ? date('d/m/Y', strtotime($row->updated_at)) : '-'
                ];
            default:
                return [];
        }
    }

    public function title(): string
    {
        return 'Laporan ' . ucfirst($this->reportType);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
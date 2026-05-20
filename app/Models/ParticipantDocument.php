<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantDocument extends Model
{
    protected $fillable = [
        'user_id',
        'programs_id',
        'document_type',
        'document_label',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'catatan',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public static function requiredDocuments(): array
    {
        return [
            'ktp'         => 'KTP (Kartu Tanda Penduduk)',
            'kk'          => 'KK (Kartu Keluarga)',
            'ijazah'      => 'Ijazah Terakhir',
            'foto'        => 'Pas Foto 3x4',
            // 'skck'        => 'SKCK (Surat Keterangan Catatan Kepolisian)',
            'bukti buat akun'   => 'Bukti Buat Akun Siap Kerja',
            'bukti assessment'  => 'Bukti Assessment Online',
            // 'surat_sehat' => 'Surat Keterangan Sehat',
            'cv'          => 'Curriculum Vitae (CV)',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke ProgramPelatihan.
     * Sesuaikan nama model jika berbeda (misal: Program, ProgramPelatihan, dll).
     */
    public function program()
    {
        return $this->belongsTo(\App\Models\Program::class, 'programs_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            default    => 'bg-yellow-100 text-yellow-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu Verifikasi',
        };
    }
}
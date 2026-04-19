<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantDocument extends Model
{
    protected $fillable = [
        'user_id',
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

    // Daftar jenis dokumen yang wajib diupload
    public static function requiredDocuments(): array
    {
        return [
            'ktp'         => 'KTP (Kartu Tanda Penduduk)',
            'kk'          => 'KK (Kartu Keluarga)',
            'ijazah'      => 'Ijazah Terakhir',
            'foto'        => 'Pas Foto 3×4',
            'skck'        => 'SKCK (Surat Keterangan Catatan Kepolisian)',
            'surat_sehat' => 'Surat Keterangan Sehat',
            'cv'          => 'Curriculum Vitae (CV)',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
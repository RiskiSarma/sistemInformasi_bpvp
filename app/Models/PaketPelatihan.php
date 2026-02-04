<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaketPelatihan extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'paket_pelatihans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'jenis_pelatihan_id',
        'tahun',
        'batch',
        'jp_harian',
        'sabtu_masuk',
        'minggu_masuk',
        'tanggal_mulai',
        'tanggal_akhir',
        'tanggal_awal_pendaftaran',
        'tanggal_akhir_pendaftaran',
        'tanggal_awal_tes_tulis',
        'tanggal_akhir_tes_tulis',
        'tanggal_awal_wawancara',
        'tanggal_akhir_wawancara',
        'tanggal_awal_daftar_ulang',
        'tanggal_akhir_daftar_ulang',
        'tanggal_pengumuman',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_akhir' => 'datetime',
        'tanggal_awal_pendaftaran' => 'datetime',
        'tanggal_akhir_pendaftaran' => 'datetime',
        'tanggal_awal_tes_tulis' => 'datetime',
        'tanggal_akhir_tes_tulis' => 'datetime',
        'tanggal_awal_wawancara' => 'datetime',
        'tanggal_akhir_wawancara' => 'datetime',
        'tanggal_awal_daftar_ulang' => 'datetime',
        'tanggal_akhir_daftar_ulang' => 'datetime',
        'tanggal_pengumuman' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // Generate integer ID khusus untuk foreign key
            if (empty($model->integer_id)) {
                // Ambil max integer_id + 1, atau pakai auto-increment logic sederhana
                $maxId = static::max('integer_id') ?? 0;
                $model->integer_id = $maxId + 1;
            }
        });
    }
    public function getRouteKeyName()
    {
        return 'id';
    }

    // =====================
    // RELASI UTAMA
    // =====================

    public function jenisPelatihan()
    {
        return $this->belongsTo(JenisPelatihan::class, 'jenis_pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =====================
    // RELASI PIVOT
    // =====================

    // Programs (Master Program)
    public function programs()
    {
        return $this->hasMany(Program::class, 'paket_pelatihan_id', 'id');
    }

    public function masterPrograms()
    {
        return $this->belongsToMany(
            MasterProgram::class,
            'programs',
            'paket_pelatihan_id',
            'master_program_id'
        );
    }

    // SubUnits
    public function allPaketUnits()
    {
        return $this->hasManyThrough(PaketPelatihanUnit::class, Program::class, 'paket_pelatihan_id', 'programs_id');
    }

    public function allPaketSubUnits()
    {
        return $this->hasManyThrough(PaketPelatihanSubUnit::class, Program::class, 'paket_pelatihan_id', 'programs_id')
            ->through(PaketPelatihanUnit::class, 'programs_id', 'id', 'paket_pelatihan_unit_id'); // Adjust jika perlu
    }

    // Peserta
    public function peserta()
    {
        return $this->belongsToMany(
            Peserta::class,
            'paket_pelatihan_peserta',
            'paket_pelatihan_id',
            'peserta_id'
        );
    }

    public function paketPeserta()
    {
        return $this->hasMany(PaketPelatihanPeserta::class, 'paket_pelatihan_id');
    }

    // PengajarSubUnits
    public function pengajarSubUnits()
    {
        return $this->belongsToMany(
            PengajarSubUnit::class,
            'paket_pelatihan_pengajar_sub_units',
            'paket_pelatihan_id',
            'pengajar_sub_unit_id'
        );
    }

    public function paketPengajarSubUnits()
    {
        return $this->hasMany(PaketPelatihanPengajarSubUnit::class, 'paket_pelatihan_id');
    }

    // KompetensiInti
    public function kompetensiInti()
    {
        return $this->belongsToMany(
            KompetensiInti::class,
            'paket_pelatihan_kompetensi_intis',
            'paket_pelatihan_id',
            'kompetensi_inti_id'
        );
    }

    public function paketKompetensiInti()
    {
        return $this->hasMany(PaketPelatihanKompetensiInti::class, 'paket_pelatihan_id');
    }

    // UnitKompetensi
    public function unitKompetensi()
    {
        return $this->belongsToMany(
            UnitKompetensi::class,
            'paket_pelatihan_unit_kompetensis',
            'paket_pelatihan_id',
            'unit_kompetensi_id'
        );
    }

    public function paketUnitKompetensi()
    {
        return $this->hasMany(PaketPelatihanUnitKompetensi::class, 'paket_pelatihan_id');
    }
}
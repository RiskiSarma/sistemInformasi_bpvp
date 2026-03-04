<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaketPelatihanSubUnit extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'paket_pelatihan_sub_units';

    protected $fillable = [
        'paket_pelatihan_unit_id',
        'master_programs_id',
        'independent_competency_units',  // ✅ NAMA KOLOM YANG BENAR
        'jp',
    ];

    protected $casts = [
        'jp' => 'integer',
    ];

    // ✅ Relasi ke Paket Pelatihan Unit
    public function paketPelatihanUnit()
    {
        return $this->belongsTo(PaketPelatihanUnit::class, 'paket_pelatihan_unit_id');
    }

    // ✅ Relasi ke Master Program
    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_programs_id');
    }

    // ✅ Relasi ke Independent Competency Unit (NAMA KOLOM DIPERBAIKI)
    public function unitKompetensi()
    {
        return $this->belongsTo(IndependentCompetencyUnit::class, 'independent_competency_units');
    }

    // Helper accessor
    public function getUnitNameAttribute()
    {
        return $this->unitKompetensi->name ?? $this->unitKompetensi->code ?? '-';
    }
}
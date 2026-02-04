<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PaketPelatihanSubUnit extends Model
{
    use SoftDeletes, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'paket_pelatihan_unit_id',
        'jp',
        'master_programs_id',
        'independent_competency_unit_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function paketPelatihanUnit()
    {
        return $this->belongsTo(PaketPelatihanUnit::class);
    }

    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_programs_id');
    }

    public function unitKompetensi()
    {
        return $this->belongsTo(IndependentCompetencyUnit::class, 'independent_competency_unit_id');
    }
}
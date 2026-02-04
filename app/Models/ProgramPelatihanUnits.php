<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProgramPelatihanUnits extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'program_pelatihan_units';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'program_pelatihan_id',
        'master_programs_id',
        'type_unit',
        'independent_competency_units_id',
        'sub_unit_kompetensi',
        'jp',
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class, 'master_programs_id');
    }

    public function independentCompetencyUnit()
    {
        return $this->belongsTo(IndependentCompetencyUnit::class, 'independent_competency_units_id');
    }
    public function programPelatihan()
    {
        return $this->belongsTo(Program::class, 'program_pelatihan_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProgramPelatihanDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'program_pelatihan_details';
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'program_id',
        'independent_competency_unit_id',
        'unit_type',
        'durasi_jp',
        'urutan',
        'is_editable',
    ];

    protected $casts = [
        'durasi_jp' => 'integer',
        'urutan' => 'integer',
        'is_editable' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function independentCompetencyUnit()
    {
        return $this->belongsTo(IndependentCompetencyUnit::class);
    }
}
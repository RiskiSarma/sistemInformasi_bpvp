<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaketPelatihanUnit extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'programs_id',
        'program_pelatihan_unit_id',
        'master_program_sub_unit_id',
        'jp',
        'sub_unit_kompetensi',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'programs_id');
    }

    public function programPelatihanUnit()
    {
        return $this->belongsTo(ProgramPelatihanUnit::class, 'program_pelatihan_unit_id');
    }

    public function masterProgramSubUnit()
    {
        return $this->belongsTo(MasterProgram::class, 'master_program_sub_unit_id');
    }
    // ✅ TAMBAHKAN METHOD INI (YANG HILANG!)
    public function paketPelatihanSubUnits()
    {
        return $this->hasMany(PaketPelatihanSubUnit::class, 'paket_pelatihan_unit_id');
    }
}
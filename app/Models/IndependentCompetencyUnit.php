<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class IndependentCompetencyUnit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'independent_competency_units';

    protected $fillable = [
        'code',
        'name',
        'description',
        'created_by',
        'updated_by',
        'skkni_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    // Otomatis set created_by dan updated_by saat create/update
    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::saving(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function skkni()
    {
        return $this->belongsTo(Skkni::class, 'skkni_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function programs()
    {
        return $this->belongsToMany(
            Program::class,
            'independent_competency_unit_program',
            'independent_competency_unit_id',
            'program_id'
        )->withTimestamps();
    }

    public function masterPrograms()
    {
        return $this->belongsToMany(
            MasterProgram::class,
            'independent_competency_unit_program',
            'independent_competency_unit_id',
            'program_id'
        );
    }

    public function programPelatihanUnits()
    {
        return $this->hasMany(ProgramPelatihanUnits::class, 'independent_competency_units_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    ];

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
        return $this->belongsToMany(MasterProgram::class, 'independent_competency_unit_program', 'independent_competency_unit_id', 'program_id');
    }
}
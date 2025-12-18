<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProgram extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'duration_hours',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_hours' => 'integer',
    ];

    // Relasi ke Programs
    public function programs()
    {
        return $this->hasMany(Program::class, 'master_program_id');
    }

    public function competencyUnits(): HasMany
    {
        return $this->hasMany(CompetencyUnit::class);
    }
}

?>

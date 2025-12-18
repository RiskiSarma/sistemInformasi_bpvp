<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyUnits extends Model
{
    protected $fillable = [
        'master_program_id',
        'code',
        'name',
        'description',
    ];

    public function masterProgram(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class);
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MasterProgram extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'duration_hours',
        'is_active',
        'created_by', 
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration_hours' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
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

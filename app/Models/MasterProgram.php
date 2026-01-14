<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterProgram extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'code',
        'name',
        'description',
        'duration_hours',
        'kejuruan',
        'bidang',
        'jenis_pelatihan',
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

    // public function competencyUnits(): HasMany
    // {
    //     return $this->hasMany(CompetencyUnit::class);
    // }

    // Accessor baru: ambil semua unit independen dari semua programs di bawah master ini
    public function getIndependentCompetencyUnitsAttribute()
    {
        return $this->programs->flatMap(function ($program) {
            return $program->independentCompetencyUnits;
        })->unique('id'); // unique biar tidak duplikat
    }

    // Accessor untuk count (biar lebih cepat)
    public function getIndependentCompetencyUnitsCountAttribute()
    {
        return $this->independent_competency_units->count();
    }

    public function getJenisPelatihanFullAttribute()
    {
        $map = [
            'PBL'           => 'Project Based Learning (PBL)',
            'Non Boarding'  => 'Non Boarding',
            'Boarding'      => 'Boarding',
            // tambahkan jenis lain di sini kalau ada
        ];

        return $map[$this->jenis_pelatihan] ?? $this->jenis_pelatihan ?? '-';
    }
    public function batches()
    {
        return $this->hasMany(Batch::class, 'master_program_id');
    }
}

?>

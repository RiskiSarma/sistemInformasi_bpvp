<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyUnit extends Model
{
    protected $fillable = [
        'master_program_id',
        'code',
        'name',
        'description',
        'created_by',
        'updated_by',
    ];

    public function masterProgram(): BelongsTo
    {
        return $this->belongsTo(MasterProgram::class);
    }
    // === RELASI AUDIT TRAIL ===
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // === BOOT METHOD (kalau belum ada) ===
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $model->created_by = \Illuminate\Support\Facades\Auth::id();
                $model->updated_by = \Illuminate\Support\Facades\Auth::id();
            }
        });

        static::updating(function ($model) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $model->updated_by = \Illuminate\Support\Facades\Auth::id();
            }
        });
    }
}
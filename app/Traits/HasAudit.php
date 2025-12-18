<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    /**
     * Boot the trait
     */
    protected static function bootHasAudit()
    {
        // Ketika membuat record baru
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Ketika mengupdate record
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Relasi ke user yang membuat record
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Relasi ke user yang terakhir mengupdate record
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
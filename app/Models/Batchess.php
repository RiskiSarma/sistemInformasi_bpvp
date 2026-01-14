<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'year',
        'period',
        'start_date',
        'end_date',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            $batch->slug = Str::slug($batch->name);
            if (auth()->check()) {
                $batch->created_by = auth()->id();
                $batch->updated_by = auth()->id();
            }
        });

        static::updating(function ($batch) {
            $batch->slug = Str::slug($batch->name);
            if (auth()->check()) {
                $batch->updated_by = auth()->id();
            }
        });
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
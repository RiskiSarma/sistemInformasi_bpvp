<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'master_program_id',
        'jenis_pelatihan_id',
        'is_active',
    ];

    public function masterProgram()
    {
        return $this->belongsTo(MasterProgram::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'batch_id');
    }
    public function jenisPelatihan()
    {
        return $this->belongsTo(JenisPelatihan::class, 'jenis_pelatihan_id');
    }
}
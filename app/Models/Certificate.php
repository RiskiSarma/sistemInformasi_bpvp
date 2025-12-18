<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'program_id',
        'certificate_number',
        'issue_date',
        'pdf_path',
        'status',
        'notes'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Generate suggested certificate number (opsional, bisa diabaikan)
    public static function generateSuggestedNumber()
    {
        $year = date('Y');
        $lastCert = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastCert ? (int)substr($lastCert->certificate_number, -4) + 1 : 1;
        
        return sprintf('BPVP/%s/CERT/%04d', $year, $number);
    }
}
?>
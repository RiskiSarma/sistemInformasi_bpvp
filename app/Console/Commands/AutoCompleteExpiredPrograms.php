<?php

namespace App\Console\Commands;

use App\Models\Program;
use Illuminate\Console\Command;

class AutoCompleteExpiredPrograms extends Command
{
    protected $signature   = 'programs:auto-complete';
    protected $description = 'Otomatis ubah status ongoing → completed jika end_date sudah lewat';

    public function handle(): int
    {
        $this->info('Mengecek program yang sudah expired...');

        $count = Program::where('status', 'ongoing')
            ->whereDate('end_date', '<', now()->toDateString())
            ->update([
                'status'     => 'completed',
                'updated_at' => now(),
            ]);

        if ($count > 0) {
            $this->info("✅ {$count} program berhasil diubah ke status 'completed'.");
        } else {
            $this->info('ℹ️  Tidak ada program yang perlu diupdate.');
        }

        return Command::SUCCESS;
    }
}
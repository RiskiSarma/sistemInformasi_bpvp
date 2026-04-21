<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\SyncKemnakerPrograms::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Sync setiap minggu pada hari Senin jam 2 pagi
        $schedule->command('kemnaker:sync-programs')->weeklyOn(1, '2:00');
        
        $schedule->command('programs:auto-complete')
        ->dailyAt('00:05')
        ->appendOutputTo(storage_path('logs/auto-complete-programs.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
    
}
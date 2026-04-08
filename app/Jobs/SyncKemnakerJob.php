<?php
// app/Jobs/SyncKemnakerJob.php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SyncKemnakerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 jam
    public $tries   = 1;

    public function __construct(public string $mode = 'update-null') {}

    public function handle(): void
    {
        Cache::put('kemnaker_sync_status', [
            'running'    => true,
            'mode'       => $this->mode,
            'started_at' => now()->toDateTimeString(),
            'message'    => 'Sedang berjalan...',
        ], 7200);

        try {
            if ($this->mode === 'full') {
                Artisan::call('kemnaker:sync-programs', [
                    '--limit'      => 50,
                    '--page'       => 1,
                    '--max-pages'  => 999,
                    '--skip-files' => true,
                ]);
            } else {
                Artisan::call('kemnaker:sync-programs', [
                    '--update-null' => true,
                    '--skip-files'  => true,
                ]);
            }

            $output = Artisan::output();

            Cache::put('kemnaker_sync_status', [
                'running'     => false,
                'mode'        => $this->mode,
                'finished_at' => now()->toDateTimeString(),
                'message'     => 'Selesai! ' . trim($output),
                'success'     => true,
            ], 7200);

        } catch (\Exception $e) {
            Cache::put('kemnaker_sync_status', [
                'running' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false,
            ], 7200);
        }
    }
}
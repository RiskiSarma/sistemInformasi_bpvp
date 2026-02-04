<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Skkni;
use App\Models\IndependentCompetencyUnit;

class SyncSkkniProglat extends Command
{
    protected $signature = 'proglat:sync-skkni 
                            {--limit=100 : Jumlah data per request} 
                            {--page=1 : Halaman awal} 
                            {--max-pages=999 : Maksimal halaman yang diproses}
                            {--skip-files : Skip download file PDF}
                            {--debug : Mode debug}';

    protected $description = 'Sinkronisasi data SKKNI, unit kompetensi, dan file PDF dari API Proglat';

    private $downloadTimeout = 90; // Increased from 30
    private $apiTimeout = 120;     // Increased from 60

    public function handle()
    {
        $this->info('🔄 Memulai sinkronisasi SKKNI dari Proglat...');

        $limit = $this->option('limit') ?? 100;
        $page = $this->option('page') ?? 1;
        $maxPages = $this->option('max-pages') ?? 999;
        $skipFiles = $this->option('skip-files');
        $debug = $this->option('debug');

        $totalSynced = 0;
        $totalUpdated = 0;
        $totalFailed = 0;
        $totalUnits = 0;
        $totalFilesDownloaded = 0;
        $totalFilesFailed = 0;
        $totalFilesSkipped = 0;

        if (!$skipFiles) {
            $this->info('📥 Mode: Sync data + Download file PDF');
            $this->ensureStorageDirectoryExists();
        } else {
            $this->warn('⏭️  Mode: Sync data saja (skip file PDF)');
        }

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $pageCount = 0;

        do {
            if ($pageCount >= $maxPages) {
                break;
            }

            $this->info("📄 Mengambil halaman {$page}...");

            try {
                $response = Http::timeout($this->apiTimeout)
                    ->retry(3, 1000)
                    ->get('https://skkni-api.kemnaker.go.id/v1/public/documents/with-units', [
                        'limit' => $limit,
                        'page'  => $page,
                    ]);

                if (!$response->successful()) {
                    $this->error("❌ Gagal ambil data halaman {$page}");
                    break;
                }

                $data = $response->json();
                $skkniList = $data['data'] ?? [];

                if (empty($skkniList)) {
                    $this->info("✅ Tidak ada data lagi di halaman {$page}");
                    break;
                }

                $this->info("   Ditemukan " . count($skkniList) . " SKKNI");

                $bar = $this->output->createProgressBar(count($skkniList));
                $bar->start();

                foreach ($skkniList as $item) {
                    try {
                        $apiId = $item['id'] ?? null;
                        $nomor = $item['number_kepmen'] ?? $item['number'] ?? null;
                        
                        if (empty($nomor)) {
                            $totalFailed++;
                            $bar->advance();
                            continue;
                        }

                        // Check if SKKNI already exists
                        $existingSkkni = Skkni::where('nomor', $nomor)->first();
                        
                        // ✅ FIX: Preserve file_path if file already downloaded
                        $skkniData = [
                            'nomor'     => $nomor,
                            'skkni'     => $item['title'] ?? null,
                            'tanggal'   => $item['published_at'] ?? $item['created_at'] ?? null,
                            'berlaku'   => ($item['status'] ?? '') === 'published' ? 'Y' : 'N',
                            'file_name' => $item['file_name'] ?? null,
                            'file_type' => 'application/pdf',
                        ];

                        // ✅ ONLY set file_path if:
                        // 1. No existing SKKNI, OR
                        // 2. Existing file_path is not a local path (still an ID or empty)
                        if (!$existingSkkni || 
                            !str_starts_with($existingSkkni->file_path ?? '', 'skkni/')) {
                            $skkniData['file_path'] = $apiId;
                        }

                        if (empty($skkniData['skkni'])) {
                            $totalFailed++;
                            $bar->advance();
                            continue;
                        }

                        $skkni = Skkni::updateOrCreate(
                            ['nomor' => $nomor],
                            $skkniData
                        );

                        if ($skkni->wasRecentlyCreated) {
                            $totalSynced++;
                        } else {
                            $totalUpdated++;
                        }

                        // === DOWNLOAD FILE ===
                        if (!$skipFiles && $apiId) {
                            // Check if file already exists locally
                            if (str_starts_with($skkni->file_path ?? '', 'skkni/') && 
                                Storage::disk('public')->exists($skkni->file_path)) {
                                $totalFilesSkipped++;
                            } else {
                                $downloadResult = $this->downloadFile($apiId, $skkni, $debug);
                                
                                if ($downloadResult === 'success') {
                                    $totalFilesDownloaded++;
                                } elseif ($downloadResult === 'failed') {
                                    $totalFilesFailed++;
                                }
                            }
                        }

                        // === SAVE UNITS ===
                        $units = $item['units'] ?? [];
                        if (is_array($units) && count($units) > 0) {
                            foreach ($units as $unitItem) {
                                try {
                                    $code = $unitItem['code'] ?? null;
                                    $name = $unitItem['title'] ?? $unitItem['name'] ?? null;
                                    $description = $unitItem['description'] ?? null;

                                    if (empty($code) || empty($name)) {
                                        continue;
                                    }

                                    IndependentCompetencyUnit::updateOrCreate(
                                        [
                                            'skkni_id' => $skkni->id,
                                            'code'     => $code,
                                        ],
                                        [
                                            'name'        => $name,
                                            'description' => $description,
                                        ]
                                    );
                                    
                                    $totalUnits++;
                                } catch (\Exception $e) {
                                    // Silent fail
                                }
                            }
                        }

                    } catch (\Exception $e) {
                        $totalFailed++;
                        if ($debug) {
                            $this->error("Error: " . $e->getMessage());
                        }
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $page++;
                $pageCount++;

                usleep(200000);

            } catch (\Exception $e) {
                $this->error("❌ Error pada halaman {$page}: " . $e->getMessage());
                break;
            }

        } while (count($skkniList) == $limit);

        $this->newLine();
        $this->info("✅ Sinkronisasi selesai!");
        $this->newLine();
        
        $summaryData = [
            ['✨ Baru ditambahkan (SKKNI)', $totalSynced],
            ['🔄 Diperbarui (SKKNI)', $totalUpdated],
            ['📦 Unit kompetensi tersimpan', $totalUnits],
            ['❌ Gagal (SKKNI)', $totalFailed],
        ];

        if (!$skipFiles) {
            $summaryData[] = ['📥 File PDF berhasil didownload', $totalFilesDownloaded];
            $summaryData[] = ['⏭️  File PDF dilewati (sudah ada)', $totalFilesSkipped];
            $summaryData[] = ['⚠️  File PDF gagal didownload', $totalFilesFailed];
        }

        $this->table(['Status', 'Jumlah'], $summaryData);

        return 0;
    }

    private function ensureStorageDirectoryExists()
    {
        $years = range(2020, date('Y') + 1);
        foreach ($years as $year) {
            $path = "skkni/{$year}";
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path);
            }
        }
    }

    private function downloadFile($apiId, $skkni, $debug = false)
    {
        try {
            $year = date('Y', strtotime($skkni->tanggal ?? 'now'));
            $filename = $skkni->file_name ?? 'SKKNI-' . str_replace(['/', ' '], '-', $skkni->nomor) . '.pdf';
            $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '-', $filename);
            $localPath = "skkni/{$year}/{$filename}";

            // Double check file doesn't exist
            if (Storage::disk('public')->exists($localPath)) {
                return 'skipped';
            }

            $url = "https://skkni-api.kemnaker.go.id/v1/public/documents/{$apiId}/download";

            if ($debug) {
                $this->line("   Trying: {$url}");
            }
            
            $response = Http::timeout($this->downloadTimeout)->get($url);
            
            if (!$response->successful()) {
                if ($debug) {
                    $this->warn("   ✗ Failed: HTTP " . $response->status());
                }
                return 'failed';
            }

            $contentType = $response->header('Content-Type');
            if (!in_array($contentType, ['application/pdf', 'application/octet-stream']) && 
                !str_contains($contentType, 'pdf')) {
                if ($debug) {
                    $this->warn("   ✗ Wrong content type: {$contentType}");
                }
                return 'failed';
            }

            $fileContent = $response->body();
            if (empty($fileContent)) {
                return 'failed';
            }

            $directory = "skkni/{$year}";
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            Storage::disk('public')->put($localPath, $fileContent);

            if (!Storage::disk('public')->exists($localPath)) {
                return 'failed';
            }

            // ✅ Update database dengan path lokal
            $skkni->update([
                'file_path' => $localPath,
                'file_name' => $filename,
            ]);

            if ($debug) {
                $this->info("   ✓ Downloaded: {$localPath}");
            }

            return 'success';

        } catch (\Exception $e) {
            if ($debug) {
                $this->error("   ❌ Exception: " . $e->getMessage());
            }
            return 'failed';
        }
    }
}
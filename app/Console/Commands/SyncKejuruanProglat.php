<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Kejuruan;

class SyncKejuruanProglat extends Command
{
    protected $signature = 'proglat:sync-kejuruan {--limit=100 : Jumlah data per request}';

    protected $description = 'Sinkronisasi data Kejuruan dari API Proglat Kemnaker';

    public function handle()
    {
        $this->info('🔄 Memulai sinkronisasi Kejuruan dari Proglat...');

        $limit = $this->option('limit') ?? 100;
        $page = 1;

        $totalSynced = 0;
        $totalUpdated = 0;
        $totalFailed = 0;

        do {
            $this->info("📄 Mengambil halaman {$page}...");

            try {
                // API endpoint untuk vocational (kejuruan)
                $response = Http::withOptions([
                    'verify' => false,
                    'timeout' => 60,
                ])->get('https://api.kemnaker.go.id/proglat/v1/vocationals', [
                    'limit' => $limit,
                    'page'  => $page,
                ]);

                if (!$response->successful()) {
                    $this->error("❌ Gagal ambil data halaman {$page}: HTTP " . $response->status());
                    Log::error('Kejuruan Sync Failed Page ' . $page, ['status' => $response->status()]);
                    break;
                }

                $data = $response->json();
                $kejuruanList = $data['data'] ?? [];

                if (empty($kejuruanList)) {
                    $this->info("✅ Tidak ada data lagi di halaman {$page}. Sinkronisasi selesai.");
                    break;
                }

                $this->info("   Ditemukan " . count($kejuruanList) . " kejuruan");

                $bar = $this->output->createProgressBar(count($kejuruanList));
                $bar->start();

                foreach ($kejuruanList as $item) {
                    try {
                        // Mapping data kejuruan dari API
                        $code = $item['code'] ?? null;
                        $name = $item['name'] ?? $item['title'] ?? $item['category'] ?? null;

                        // Skip kalau nama atau code kosong
                        if (empty($name)) {
                            $totalFailed++;
                            Log::warning('Kejuruan skipped - nama kosong', ['item' => $item]);
                            $bar->advance();
                            continue;
                        }

                        // Generate code jika tidak ada dari API
                        if (empty($code)) {
                            $code = $this->generateCode($name);
                            Log::warning('Generated code untuk kejuruan', [
                                'name' => $name, 
                                'generated_code' => $code
                            ]);
                        }

                        // Simpan / update Kejuruan
                        $kejuruan = Kejuruan::updateOrCreate(
                            ['code' => $code], // Cari berdasarkan code (unique)
                            ['kejuruan' => $name]
                        );

                        if ($kejuruan->wasRecentlyCreated) {
                            $totalSynced++;
                        } else {
                            $totalUpdated++;
                        }

                    } catch (\Exception $e) {
                        $totalFailed++;
                        Log::error('Kejuruan Sync Item Failed', [
                            'item'  => $item,
                            'error' => $e->getMessage()
                        ]);
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $page++;
                sleep(1); // Delay agar tidak overload API

            } catch (\Exception $e) {
                $this->error("❌ Error pada halaman {$page}: " . $e->getMessage());
                Log::error('Kejuruan Sync Error', ['error' => $e->getMessage(), 'page' => $page]);
                break;
            }

        } while (count($kejuruanList) == $limit);

        // Ringkasan akhir
        $this->newLine();
        $this->info("✅ Sinkronisasi Kejuruan selesai!");
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['✨ Baru ditambahkan', $totalSynced],
                ['🔄 Diperbarui', $totalUpdated],
                ['❌ Gagal', $totalFailed],
                ['📊 Total diproses', $totalSynced + $totalUpdated + $totalFailed],
            ]
        );

        return 0;
    }

    /**
     * Generate code from kejuruan name
     * Contoh: 
     * - "Pariwisata" -> "PAR"
     * - "Teknologi Informasi" -> "TI"
     * - "Fashion Technology" -> "FTE"
     */
    private function generateCode($name)
    {
        $words = explode(' ', strtoupper(trim($name)));
        
        if (count($words) === 1) {
            // Single word: ambil 3 huruf pertama
            return substr($words[0], 0, 3);
        } elseif (count($words) === 2) {
            // Two words: ambil 2 huruf pertama dari kata 1, 1 huruf dari kata 2
            return substr($words[0], 0, 2) . substr($words[1], 0, 1);
        } else {
            // Three or more words: ambil 1 huruf dari 3 kata pertama
            return substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
        }
    }
}
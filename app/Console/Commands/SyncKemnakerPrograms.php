<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MasterProgram;
use App\Models\IndependentCompetencyUnit;
use Exception;

class SyncKemnakerPrograms extends Command
{
    protected $signature = 'kemnaker:sync-programs {--page=1 : Halaman API} {--limit=50 : Jumlah per halaman}';
    protected $description = 'Sync program pelatihan dan unit kompetensi dari API Kemnaker';

    public function handle()
    {
        $page = $this->option('page');
        $limit = $this->option('limit');
        $url = "https://api.kemnaker.go.id/proglat/v1/public/programs?limit={$limit}&page={$page}&hasCode=false&sortBy=created_at&sortOrder=desc";

        $this->info("Mengambil data dari: {$url}");

        try {
            $response = Http::withOptions([
                'verify' => false, // bypass SSL di local
                'timeout' => 120,  // timeout 2 menit
                'connect_timeout' => 60,
            ])->retry(5, 10000)->get($url); // retry 5 kali, delay 10 detik

            if ($response->failed()) {
                $this->error('Gagal mengambil data dari API Kemnaker (status ' . $response->status() . ').');
                $this->warn('API mungkin sedang down atau lambat. Coba lagi nanti.');
                return 1;
            }

            $json = $response->json();

            if (!isset($json['data']) || empty($json['data'])) {
                $this->info('Tidak ada data program baru dari Kemnaker saat ini.');
                return 0;
            }

            $data = $json['data'];
            $synced = 0;

            foreach ($data as $item) {
                if (!isset($item['code']) || !isset($item['title'])) {
                    continue;
                }

                $master = MasterProgram::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'name' => $item['title'],
                        'description' => $item['description'] ?? null,
                        'duration_hours' => $item['total_duration'] ?? 0,
                        'kejuruan' => $item['vocational']['title'] ?? null,
                        'bidang' => $item['sub_vocational']['title'] ?? null,
                        'jenis_pelatihan' => 'Non Boarding', // default
                        'is_active' => ($item['status'] ?? 'pending') === 'approved' ? 1 : 0,
                    ]
                );

                if (!empty($item['program_topics'])) {
                    foreach ($item['program_topics'] as $topic) {
                        if (!isset($topic['code']) || !isset($topic['title'])) {
                            continue;
                        }

                        IndependentCompetencyUnit::updateOrCreate(
                            ['code' => $topic['code']],
                            [
                                'name' => $topic['title'],
                                'description' => $topic['description'] ?? null,
                                'master_program_id' => $master->id,
                            ]
                        );
                    }
                }

                $synced++;
            }

            $this->info("Berhasil sync {$synced} program pelatihan dan unit kompetensi dari Kemnaker.");
            return 0;

        } catch (Exception $e) {
            $this->error('Error saat sync dari Kemnaker: ' . $e->getMessage());
            $this->warn('API Kemnaker sedang tidak stabil. Coba lagi dalam beberapa menit/jam.');
            return 1;
        }
    }
}
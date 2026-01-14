<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\MasterProgram;
use App\Models\Batch;

class MigrateJenisPelatihanToBatchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua master program yang sudah ada
        $masters = MasterProgram::all();

        $this->command->info("Jumlah master program lokal: " . $masters->count());

        // 2. Tarik data dari API Proglat (contoh endpoint, sesuaikan dengan punyamu)
        $apiUrl = 'https://api.kemnaker.go.id/proglat/v1/public/programs?limit=9&page=1&keyword=&vocational=&sub_vocational=&status=&hasCode=false&sortBy=created_at&sortOrder=desc';
        $response = Http::get($apiUrl, [
            'limit' => 100,   // sesuaikan limit
            'page'  => 1,
        ]);

        if ($response->failed()) {
            $this->command->error("Gagal tarik API Proglat: " . $response->status());
            return;
        }

        $apiData = $response->json()['data'] ?? []; // sesuaikan struktur JSON API

        $this->command->info("Jumlah program dari API: " . count($apiData));

        // 3. Buat map jenis_pelatihan dari API (key = code atau name master)
        $apiJenisMap = [];
        foreach ($apiData as $item) {
            $code = $item['code'] ?? null; // sesuaikan field code di API
            $jenis = $item['jenis_pelatihan'] ?? $item['jenis'] ?? 'Tidak Diketahui'; // sesuaikan field

            if ($code) {
                $apiJenisMap[$code] = $jenis;
            }
        }

        // 4. Proses setiap master lokal
        $batchNumber = 1; // nomor urut global untuk nama batch

        foreach ($masters as $master) {
            // Cek kalau master ini sudah punya batch
            if (Batch::where('master_program_id', $master->id)->exists()) {
                $this->command->info("Master {$master->name} sudah punya batch, skip...");
                continue;
            }

            // Ambil jenis dari API kalau cocok code
            $jenis = 'Tidak Diketahui';
            if (isset($apiJenisMap[$master->code])) {
                $jenis = $apiJenisMap[$master->code];
                $this->command->info("Jenis dari API untuk {$master->name}: {$jenis}");
            } else {
                $this->command->info("Tidak ketemu di API untuk {$master->name}, pakai default");
            }

            // Buat batch baru
            Batch::create([
                'code' => 'B' . str_pad($batchNumber, 4, '0', STR_PAD_LEFT),
                'name' => "Batch {$batchNumber}",  // hanya Batch 1, Batch 2, dst
                'master_program_id' => $master->id,
                'jenis_pelatihan' => $jenis,
                'is_active' => true,
            ]);

            $this->command->info("Batch {$batchNumber} dibuat untuk master {$master->name} - Jenis: {$jenis}");

            $batchNumber++;
        }

        $this->command->info("Selesai! Total batch dibuat: " . ($batchNumber - 1));
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\JenisPelatihan;

class MigrateBatchJenisStringToIdSeeder extends Seeder
{
    public function run(): void
    {
        $batches = Batch::whereNotNull('jenis_pelatihan')->whereNull('jenis_pelatihan_id')->get();

        foreach ($batches as $batch) {
            $jenisString = trim($batch->jenis_pelatihan);

            // Cari atau buat jenis pelatihan baru dari string lama
            $jenis = JenisPelatihan::firstOrCreate(
                ['jenis_pelatihan' => $jenisString],
                ['user_id' => auth()->id() ?? 1]
            );

            $batch->jenis_pelatihan_id = $jenis->id;
            $batch->save();

            $this->command->info("Batch ID {$batch->id} ({$batch->name}) dipetakan ke Jenis ID {$jenis->id} ({$jenis->jenis_pelatihan})");
        }

        $this->command->info('Migrasi selesai!');
    }
}
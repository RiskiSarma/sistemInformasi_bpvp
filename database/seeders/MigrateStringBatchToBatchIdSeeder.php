<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Batch;

class MigrateStringBatchToBatchIdSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::whereNotNull('batch')->get(); // hapus kondisi whereNull('batch_id') dulu

        foreach ($programs as $program) {
            if ($program->batch_id) continue; // skip kalau sudah punya batch_id

            // Cari batch dengan nama persis sama
            $batch = Batch::where('name', $program->batch)
                          ->where('master_program_id', $program->master_program_id)
                          ->first();

            if (!$batch) {
                // Buat baru kalau tidak ketemu
                $batch = Batch::create([
                    'code' => 'B' . str_pad($program->id, 4, '0', STR_PAD_LEFT),
                    'name' => $program->batch,
                    'master_program_id' => $program->master_program_id,
                    'jenis_pelatihan' => 'Non Boarding', // default, bisa edit nanti
                    'is_active' => true,
                ]);
            }

            $program->batch_id = $batch->id;
            $program->save();

            $this->command->info("Program ID {$program->id} ({$program->batch}) dipetakan ke Batch ID {$batch->id} ({$batch->name})");
        }

        $this->command->info('Migrasi selesai!');
    }
}
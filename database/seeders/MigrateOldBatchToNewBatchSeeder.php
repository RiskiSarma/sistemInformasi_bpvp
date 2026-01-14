<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Batch;

class MigrateOldBatchToNewBatchSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::whereNotNull('batch')->whereNull('batch_id')->get();

        $this->command->info("Program lama yang perlu migrasi: {$programs->count()}");

        foreach ($programs as $program) {
            // Cari atau buat batch berdasarkan nama batch lama + master
            $batch = Batch::firstOrCreate(
                [
                    'name' => $program->batch,
                    'master_program_id' => $program->master_program_id,
                ],
                [
                    'code' => 'B' . str_pad($program->id, 4, '0', STR_PAD_LEFT),
                    'jenis_pelatihan' => $program->masterProgram->jenis_pelatihan ?? 'Tidak Diketahui',
                    'is_active' => true,
                ]
            );

            // Update program lama ke batch_id baru
            $program->batch_id = $batch->id;
            $program->save();

            $this->command->info("Program ID {$program->id} ({$program->batch}) dimigrasi ke Batch ID {$batch->id} ({$batch->name})");
        }

        $this->command->info('Migrasi data batch lama selesai!');
    }
}
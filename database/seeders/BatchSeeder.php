<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\MasterProgram;
use App\Models\Program;

class BatchSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua master program
            $masters = MasterProgram::all();

            foreach ($masters as $master) {
                // Buat batch default untuk setiap master
                $batch = Batch::create([
                    'code' => 'B' . str_pad($master->id, 3, '0', STR_PAD_LEFT),
                    'name' => $master->name . ' - Batch Default',
                    'master_program_id' => $master->id,
                    'jenis_pelatihan' => $master->jenis_pelatihan ?? 'Non Boarding', // pindah dari master
                    'is_active' => true,
                ]);

            // Update semua program yang pakai master ini ke batch baru
            Program::where('master_program_id', $master->id)->update(['batch_id' => $batch->id]);
        }

        $this->command->info('Batch berhasil dimigrasi dari master program!');
    }
}
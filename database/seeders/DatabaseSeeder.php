<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterProgram;
use App\Models\Program;
use App\Models\Participant;
use App\Models\Instructor;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin BPVP',
            'email' => 'admin@bpvp.ac.id',
            'password' => bcrypt('password'),
        ]);

        // Create master programs
        $masterPrograms = [
            [
                'code' => 'MP001',
                'name' => 'Pelatihan Menjahit',
                'description' => 'Program pelatihan menjahit tingkat dasar hingga mahir',
                'duration_hours' => 120,
                'is_active' => true,
            ],
            [
                'code' => 'MP002',
                'name' => 'Pelatihan Digital Marketing',
                'description' => 'Program pelatihan digital marketing dan social media',
                'duration_hours' => 80,
                'is_active' => true,
            ],
            [
                'code' => 'MP003',
                'name' => 'Pelatihan Kecantikan',
                'description' => 'Program pelatihan tata rias dan kecantikan',
                'duration_hours' => 100,
                'is_active' => true,
            ],
        ];

        foreach ($masterPrograms as $mp) {
            MasterProgram::create($mp);
        }

        // Create programs
        Program::create([
            'master_program_id' => 1,
            'batch' => 'Batch I',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(40),
            'status' => 'planned',
            'max_participants' => 20,
        ]);

        Program::create([
            'master_program_id' => 2,
            'batch' => 'Batch II',
            'start_date' => now()->subDays(5),
            'end_date' => now()->addDays(20),
            'status' => 'ongoing',
            'max_participants' => 25,
        ]);

        // Create participants
        $names = ['Aisyah', 'Budi', 'Citra', 'Dedi', 'Eka', 'Fauzi', 'Gina', 'Hari'];
        foreach ($names as $index => $name) {
            Participant::create([
                'program_id' => ($index % 2) + 1,
                'name' => $name,
                'email' => strtolower($name) . '@example.com',
                'phone' => '0812345678' . $index,
                'address' => 'Banda Aceh',
                'education' => 'SMA',
                'status' => ['active', 'graduated', 'active'][$index % 3],
            ]);
        }

        // Create instructors
        Instructor::create([
            'name' => 'Siti Rahmah',
            'email' => 'siti@bpvp.ac.id',
            'phone' => '081234567890',
            'expertise' => 'Menjahit',
            'experience_years' => 8,
            'status' => 'active',
        ]);

        Instructor::create([
            'name' => 'Rahmat Digital',
            'email' => 'rahmat@bpvp.ac.id',
            'phone' => '081234567891',
            'expertise' => 'Digital Marketing',
            'experience_years' => 5,
            'status' => 'active',
        ]);
    }
}
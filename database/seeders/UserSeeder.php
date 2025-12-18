<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin BLK Banda Aceh',
            'email' => 'admin@blk.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Instructor
        User::create([
            'name' => 'Instructor BLK',
            'email' => 'instructor@blk.ac.id',
            'password' => Hash::make('instructor123'),
            'role' => 'instructor',
        ]);

        // Participant
        User::create([
            'name' => 'Peserta BLK',
            'email' => 'participant@blk.ac.id',
            'password' => Hash::make('participant123'),
            'role' => 'participant',
        ]);
    }
}
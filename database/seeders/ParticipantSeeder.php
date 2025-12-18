<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Participant;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'participant')->first();

        Participant::create([
            'user_id' => $user->id,
            'program_id' => 1, // pastikan program ini ADA
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '08123456780',
            'address' => 'Banda Aceh',
            'education' => 'SMA',
            'status' => 'active',
        ]);
    }
}

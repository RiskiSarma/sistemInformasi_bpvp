<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Participant;

class SyncParticipantsSeeder extends Seeder
{
    /**
     * Sync existing users with participant role to participants table
     */
    public function run(): void
    {
        // Get all users with role participant
        $participantUsers = User::where('role', 'participant')->get();

        foreach ($participantUsers as $user) {
            // Check if participant record already exists
            $existingParticipant = Participant::where('user_id', $user->id)->first();

            if (!$existingParticipant) {
                // Create new participant record
                Participant::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => null,
                    'phone' => null,
                    'address' => null,
                    'program_id' => null, // NULL bukan string kosong
                    'status' => 'active',
                ]);

                $this->command->info("✓ Created participant record for: {$user->email}");
            } else {
                // Update existing participant with name and email
                $existingParticipant->update([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);

                $this->command->info("✓ Updated participant record for: {$user->email}");
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('✓ Sync completed successfully!');
        $this->command->info("Total processed: {$participantUsers->count()} users");
        $this->command->info('========================================');
    }
}
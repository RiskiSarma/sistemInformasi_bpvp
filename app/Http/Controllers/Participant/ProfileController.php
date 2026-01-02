<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Participant;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        
        // Ambil data participant
        $participant = Participant::where('user_id', $user->id)->first();
        
        return view('participant-area.profile.edit', compact('user', 'participant'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $participant = Participant::where('user_id', $user->id)->firstOrFail(); // lebih aman

        // Validasi data user
        $validatedUser = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Validasi data participant (termasuk NIK)
        $validatedParticipant = $request->validate([
            'nik'     => 'required|string|size:16', 
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date'  => 'nullable|date|before_or_equal:today',
        ]);

        // Update user
        $user->update($validatedUser);

        // Update participant
        $participant->update($validatedParticipant);

        return redirect()->route('participant.profile.edit')
            ->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('participant.profile.edit')
            ->with('success', 'Password berhasil diubah');
    }
}
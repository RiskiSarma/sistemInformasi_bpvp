<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        
        // Cek apakah user adalah instruktur internal atau eksternal
        $instructor = \App\Models\Instructor::where('user_id', $user->id)->first();
        $pengajarEksternal = \App\Models\PengajarEksternal::where('user_id', $user->id)->first();
        
        // Load relasi pendidikan
        if ($instructor) {
            $instructor->load('pendidikan');
        }
        if ($pengajarEksternal) {
            $pengajarEksternal->load('pendidikan');
        }
        
        // Data master untuk dropdown
        $pendidikans = \App\Models\Pendidikan::orderBy('id', 'asc')->get();
        
        return view('instructor-area.profile.edit', compact('user', 'instructor', 'pengajarEksternal', 'pendidikans'));
    }
    
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'expertise' => 'nullable|string|max:255',
            'pendidikan_id' => 'nullable|exists:pendidikans,id',
            'experience_years' => 'nullable|integer|min:0',
            // Untuk pengajar eksternal
            'nik' => 'nullable|string|max:20',
            'nip' => 'nullable|string|max:20',
            'instansi' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kejuruan_pendidikan' => 'nullable|string|max:255',
        ]);
        
        // Update data user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        // Update data instructor internal jika ada
        $instructor = \App\Models\Instructor::where('user_id', $user->id)->first();
        if ($instructor) {
            $instructor->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'expertise' => $validated['expertise'] ?? null,
                'pendidikan_id' => $validated['pendidikan_id'] ?? null,
                'experience_years' => $validated['experience_years'] ?? null,
            ]);
        }
        
        // Update data pengajar eksternal jika ada
        $pengajarEksternal = \App\Models\PengajarEksternal::where('user_id', $user->id)->first();
        if ($pengajarEksternal) {
            $pengajarEksternal->update([
                'nama' => $validated['name'],
                'email' => $validated['email'],
                'telepon' => $validated['phone'] ?? null,
                'nik' => $validated['nik'] ?? null,
                'nip' => $validated['nip'] ?? null,
                'instansi' => $validated['instansi'] ?? null,
                'jabatan' => $validated['jabatan'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'pendidikan_id' => $validated['pendidikan_id'] ?? null,
                'kejuruan_pendidikan' => $validated['kejuruan_pendidikan'] ?? null,
            ]);
        }
        
        return redirect()->route('instructor.profile.edit')
            ->with('success', 'Profil berhasil diperbarui');
    }
    
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('instructor.profile.edit')
            ->with('success', 'Password berhasil diubah');
    }
}
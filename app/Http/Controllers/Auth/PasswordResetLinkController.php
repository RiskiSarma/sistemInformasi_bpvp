<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordResetLinkController extends Controller
{
    /**
     * Tampilkan halaman lupa password
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses lupa password: generate password baru & tampilkan di layar
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'], // pastikan email ada di tabel users
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Generate password baru acak (12 karakter, campur huruf besar-kecil-angka)
        $newPassword = Str::random(12);

        // Update password di database
        $user->password = Hash::make($newPassword);
        $user->save();

        // Kembalikan ke halaman yang sama dengan password baru
        return back()->with('new_password', $newPassword);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Participant;
use App\Models\Program;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isInstructor()) {
                return redirect()->intended(route('instructor.dashboard'));
            } else {
                return redirect()->intended(route('participant.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        $programs = Program::with('masterProgram')
            ->whereIn('status', ['planned', 'ongoing'])
            ->get();

        return view('auth.register', compact('programs'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'phone'      => 'required|string|max:20',
            'nik'        => 'nullable|digits:16|unique:participants,nik',
            'program_id' => 'required|exists:programs,id',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'participant',
        ]);

        Participant::create([
            'user_id'    => $user->id,
            'program_id' => $validated['program_id'],
            'nik'        => $validated['nik'] ?? null,
            'phone'      => $validated['phone'],
            'status'     => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('participant.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
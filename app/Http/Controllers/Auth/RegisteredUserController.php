<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Participant;
use App\Models\Program;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Hanya ambil program yang statusnya 'planned' (sedang dibuka pendaftaran)
        $programs = Program::with('masterProgram') // join dengan master_programs untuk ambil nama
            ->where('status', 'planned')
            ->get();

        return view('auth.register', compact('programs'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            // 'program_id' => ['required', 'exists:programs,id'],
            'nik' => ['nullable', 'string', 'max:20', 'unique:participants'],
        ]);

        // FORCE role to participant for public registration
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'participant', // ALWAYS participant for public registration
        ]);

        // Auto-create participant record (TANPA name dan email!)
        Participant::create([
            'user_id' => $user->id,
            'program_id' => $request->program_id,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'status' => 'active',
            'enrollment_date' => now(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to dashboard (will auto-redirect to participant dashboard)
        return redirect()->route('dashboard');
    }
}
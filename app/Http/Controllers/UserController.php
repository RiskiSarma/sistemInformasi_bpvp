<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Instructor;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['instructor', 'participant', 'creator', 'updater']);
        
        $users = User::with(['instructor', 'participant', 'creator'])
                    ->orderBy('name')
                    ->paginate(15);
        
        // Cari nama atau email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15);

        // Agar link pagination tetap membawa parameter search & role
        $users->appends($request->query());
        $users->load('updater');
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,instructor,participant',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        // Buat profil kosong kalau perlu
        if ($user->isInstructor()) {
            Instructor::create(['user_id' => $user->id]);
        } elseif ($user->isParticipant()) {
            Participant::create(['user_id' => $user->id]);
        }

        // === TAMBAHKAN NOTIFIKASI DI SINI ===
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $user,
            auth()->user(),
            'User',
            'ditambahkan'
        ));

        if ($user->isInstructor()) {
            Instructor::create(['user_id' => $user->id]);
        } elseif ($user->isParticipant()) {
            Participant::create(['user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat!');
    }

    public function edit(User $user)
    {
        $user->load(['creator', 'updater']);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'role'     => 'required|in:admin,instructor,participant',
        ]);

        $user->update([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);
        
        $user->touch();

        // === TAMBAHKAN NOTIFIKASI DI SINI ===
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $user,
            auth()->user(),
            'User',
            'diperbarui'
        ));
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }
}
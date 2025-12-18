<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParticipantDashboardController extends Controller
{
    /**
     * Display participant dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $participant = $user->participant;

        // Jika belum link ke participant, redirect ke profile untuk lengkapi data
        if (!$participant) {
            return redirect()->route('participant.profile')
                ->with('warning', 'Silakan lengkapi data profil Anda terlebih dahulu.');
        }

        $program = $participant->program;
        
        // Statistik kehadiran
        $totalAttendances = $participant->attendances()->count();
        $presentCount = $participant->attendances()->where('status', 'present')->count();
        $attendancePercentage = $totalAttendances > 0 
            ? round(($presentCount / $totalAttendances) * 100, 2) 
            : 0;

        // Kehadiran terbaru
        $recentAttendances = $participant->attendances()
            ->with('program')
            ->latest('date')
            ->take(5)
            ->get();

        return view('participant.dashboard', compact(
            'user',
            'participant',
            'program',
            'totalAttendances',
            'presentCount',
            'attendancePercentage',
            'recentAttendances'
        ));
    }

    /**
     * Show participant profile
     */
    public function profile()
    {
        $user = Auth::user();
        $participant = $user->participant;

        return view('participant.profile', compact('user', 'participant'));
    }

    /**
     * Update participant profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|confirmed|min:8',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update password jika ada
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        // Update participant data jika sudah ada
        if ($user->participant) {
            $user->participant->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $user->participant->phone,
                'address' => $validated['address'] ?? $user->participant->address,
            ]);
        }

        return redirect()->route('participant.profile')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Show participant schedule
     */
    public function schedule()
    {
        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant || !$participant->program) {
            return view('participant.schedule', [
                'program' => null,
                'message' => 'Anda belum terdaftar di program pelatihan.'
            ]);
        }

        $program = $participant->program()
            ->with('masterProgram')
            ->first();

        return view('participant.schedule', compact('program'));
    }

    /**
     * Show participant attendance
     */
    public function attendance()
    {
        $user = Auth::user();
        $participant = $user->participant;

        if (!$participant) {
            return view('participant.attendance', [
                'attendances' => collect(),
                'stats' => null
            ]);
        }

        $attendances = $participant->attendances()
            ->with('program')
            ->latest('date')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => $participant->attendances()->count(),
            'present' => $participant->attendances()->where('status', 'present')->count(),
            'absent' => $participant->attendances()->where('status', 'absent')->count(),
            'late' => $participant->attendances()->where('status', 'late')->count(),
            'excused' => $participant->attendances()->where('status', 'excused')->count(),
        ];

        $stats['percentage'] = $stats['total'] > 0 
            ? round(($stats['present'] / $stats['total']) * 100, 2)
            : 0;

        return view('participant.attendance', compact('attendances', 'stats'));
    }

    /**
     * Show materials
     */
    public function materials()
    {
        $user = Auth::user();
        $participant = $user->participant;

        $program = $participant ? $participant->program : null;

        return view('participant.materials', compact('program'));
    }

    /**
     * Show certificate
     */
    public function certificate()
    {
        $user = Auth::user();
        $participant = $user->participant;

        $canDownload = $participant && $participant->status === 'graduated';

        return view('participant.certificate', compact('participant', 'canDownload'));
    }

    /**
     * Show notifications
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('participant.notifications', compact('notifications', 'unreadCount'));
    }
}
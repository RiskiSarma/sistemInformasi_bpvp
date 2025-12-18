<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with(['user', 'program', 'creator', 'updater']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Search by name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
        }

        $participants = $query->orderby('id','asc')->paginate(15);
        $programs = Program::where('status', 'active')->get();

        return view('participants.index', compact('participants', 'programs'));
    }

    public function show(Participant $participant)
    {
        // Load semua relasi yang dibutuhkan
        $participant->load(['user', 'program.masterProgram', 'creator', 'updater', 'attendances']);

        // Hitung statistik kehadiran
        $attendances = $participant->attendances; // asumsi relasi attendances sudah ada
        $totalAttendances = $attendances->count();

        if ($totalAttendances > 0) {
            $presentCount = $attendances->where('status', 'present')->count();
            $absentCount  = $attendances->where('status', 'absent')->count();
            $lateCount    = $attendances->where('status', 'late')->count();
            $excusedCount = $attendances->where('status', 'excused')->count(); // atau 'izin'

            $attendancePercentage = round(($presentCount / $totalAttendances) * 100, 2);
        } else {
            $presentCount = $absentCount = $lateCount = $excusedCount = 0;
            $attendancePercentage = 0;
        }

        return view('participants.show', compact(
            'participant',
            'attendancePercentage',
            'totalAttendances',
            'presentCount',
            'absentCount',
            'lateCount',
            'excusedCount'
        ));
    }

    public function create()
    {
        $programs = Program::with('masterProgram')
            ->whereIn('status', ['planned', 'ongoing'])
            ->get();
        
        return view('participants.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'education' => 'nullable|string|max:100',
            'nik' => ['nullable', 'string', 'max:20', 'unique:participants'],
            'status' => 'required|in:active,graduated,dropout',
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'participant',
        ]);

        // Create participant (created_by akan otomatis terisi oleh HasAudit trait)
        Participant::create([
            'user_id' => $user->id,
            'program_id' => $request->program_id,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'address' => $request->address,
            'batch' => $request->batch,
            'status' => 'active',
            'enrollment_date' => now(),
        ]);

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function edit(Participant $participant)
    {
        $participant->load('user', 'program.masterProgram');
        $programs = Program::with('masterProgram')
        ->whereIn('status', ['planned', 'ongoing', 'active'])
        ->get();

        return view('participants.edit', compact('participant', 'programs'));
    }

    public function update(Request $request, Participant $participant)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $participant->user_id],
        'phone' => ['required', 'string', 'max:20'],
        'program_id' => ['required', 'exists:programs,id'],
        'nik' => ['nullable', 'string', 'max:20', 'unique:participants,nik,' . $participant->id],
        'address' => ['nullable', 'string'],
        // 'batch' => ['nullable', 'string', 'max:50'],  // HAPUS VALIDASI INI JUGA
        'status' => ['required', 'in:active,inactive,graduated'],
    ]);

    // Update user
    $participant->user->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    // Update participant — HAPUS 'batch'
    $participant->update([
        'program_id' => $request->program_id,
        'nik' => $request->nik,
        'phone' => $request->phone,
        'address' => $request->address,
        'status' => $request->status,
        'education' => $request->education,

    ]);

    return redirect()->route('admin.participants.index')
        ->with('success', 'Data peserta berhasil diperbarui!');
}

    public function destroy(Participant $participant)
    {
        $user = $participant->user;
        $participant->delete();
        $user->delete();

        return redirect()->route('participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with(['user', 'program.masterProgram', 'creator', 'updater']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%");
        }

        $participants = $query->orderBy('id', 'asc')->paginate(15);
        $programs = Program::whereIn('status', ['planned', 'ongoing', 'completed'])->get();

        return view('participants.index', compact('participants', 'programs'));
    }

    public function create()
    {
        $programs = Program::with('masterProgram')->get();
        $users = User::where('role', 'participant')
                     ->whereDoesntHave('participant') // user participant yang belum punya profil peserta
                     ->orderBy('name')
                     ->get();

        return view('participants.create', compact('programs', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|exists:users,id|unique:participants,user_id',
            'program_id'  => 'required|exists:programs,id',
            'nik'         => 'nullable|string|max:16|unique:participants,nik',
            'phone'       => 'nullable|string|max:20',
            'education'   => 'nullable|string|max:100',
            'address'     => 'nullable|string',
            'status'      => 'required|in:active,graduated,dropout',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date|before_or_equal:today',
        ]);

        $participant = Participant::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // NOTIFIKASI TAMBAH PESERTA
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $participant,
            auth()->user(),
            'Peserta Pelatihan',
            'ditambahkan'
        ));

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function edit(Participant $participant)
    {
        $participant->load(['user', 'program.masterProgram']);
        $programs = Program::with('masterProgram')->get();

        return view('participants.edit', compact('participant', 'programs'));
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nik'        => ['nullable', 'string', 'max:16', Rule::unique('participants', 'nik')->ignore($participant->id)],
            'phone'      => 'nullable|string|max:20',
            'education'  => 'nullable|string|max:100',
            'address'    => 'nullable|string',
            'status'     => 'required|in:active,graduated,dropout',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date|before_or_equal:today',
        ]);

        $participant->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        $participant->user->updated_by = auth()->id();
        $participant->user->touch();
        $participant->user->save();
        
        // NOTIFIKASI UBAH PESERTA
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $participant,
            auth()->user(),
            'Peserta Pelatihan',
            'diperbarui'
        ));

        return redirect()->route('admin.participants.index')
            ->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function show(Participant $participant)
    {
        $participant->load(['user', 'program.masterProgram', 'creator', 'updater', 'attendances']);

        $attendances = $participant->attendances;
        $totalAttendances = $attendances->count();

        if ($totalAttendances > 0) {
            $presentCount = $attendances->where('status', 'present')->count();
            $absentCount  = $attendances->where('status', 'absent')->count();
            $lateCount    = $attendances->where('status', 'late')->count();
            $excusedCount = $attendances->where('status', 'excused')->count();

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

    public function destroy(Participant $participant)
    {
        $userId = $participant->user_id;
        $participant->delete();

        // Hapus user kalau tidak dipakai lagi (opsional)
        if (User::find($userId)->participant()->doesntExist()) {
            User::find($userId)->delete();
        }

        return redirect()->route('admin.participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use App\Imports\ParticipantImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with(['user', 'program.masterProgram', 'program', 'creator', 'updater']);

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
            // 'education'   => 'nullable|string|max:100',
            'pendidikan_id' => 'required|exists:pendidikans,id',
            'address'     => 'nullable|string',
            'status'      => 'required|in:active,graduated,dropout',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date|before_or_equal:today',
            'gender'      => 'required|in:Laki-laki,Perempuan',
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
            // 'education'  => 'nullable|string|max:100',
            'address'    => 'nullable|string',
            'status'     => 'required|in:active,graduated,dropout',
            'birth_place'  => 'nullable|string|max:100',
            'birth_date'   => 'nullable|date|before_or_equal:today',
            'gender'      => 'required|in:Laki-laki,Perempuan',
            'pendidikan_id' => 'required|exists:pendidikans,id',
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
        $participant->load(['user', 'program.paketPelatihan','program.masterProgram', 'program', 'creator', 'updater', 'attendances']);

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
    // Tampilkan halaman import
    public function importForm()
    {
        $programs = Program::with('masterProgram')
            ->whereIn('status', ['planned', 'ongoing'])
            ->get();

        return view('participants.import', compact('programs'));
    }

    // Proses import
    // Proses import
    public function import(Request $request)
    {
        $request->validate([
            'file'       => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'program_id' => 'required|exists:programs,id',
        ]);

        $import = new ParticipantImport(
            $request->program_id,
            auth()->id()
        );

        Excel::import($import, $request->file('file'));

        $parts = [];
        if ($import->importedCount > 0) $parts[] = "{$import->importedCount} peserta baru ditambahkan";
        if ($import->updatedCount > 0)  $parts[] = "{$import->updatedCount} peserta diperbarui";
        if ($import->filteredCount > 0) $parts[] = "{$import->filteredCount} dilewati (beda program)";
        if ($import->skippedCount > 0)  $parts[] = "{$import->skippedCount} dilewati (data kosong)";

        $message = "Import selesai: " . implode(', ', $parts) . ".";

        // Kirim notifikasi hanya jika ada yang berhasil diimport atau diupdate
        if ($import->importedCount > 0 || $import->updatedCount > 0) {
            $program     = Program::with('masterProgram')->find($request->program_id);
            $programName = $program?->masterProgram?->name ?? 'Program';

            $notifParts = [];
            if ($import->importedCount > 0) $notifParts[] = "{$import->importedCount} peserta baru";
            if ($import->updatedCount > 0)  $notifParts[] = "{$import->updatedCount} diperbarui";
            $notifDetail = implode(', ', $notifParts);

            // Ambil participant pertama yang baru diimport sebagai model referensi
            // agar GeneralActivityNotification tidak error saat akses properti model
            $participantRef = Participant::where('program_id', $request->program_id)
                ->latest('created_at')
                ->first();

            if ($participantRef) {
                $admins = User::where('role', 'admin')->get();
                Notification::send($admins, new GeneralActivityNotification(
                    $participantRef,
                    auth()->user(),
                    "Import Peserta ({$programName})",
                    "selesai: {$notifDetail}"
                ));
            }
        }

        if (!empty($import->importErrors)) {
            return redirect()->route('admin.participants.index')
                ->with('warning', $message)
                ->with('import_errors', $import->importErrors);
        }

        return redirect()->route('admin.participants.index')
            ->with('success', $message);
    }

    // Download template Excel
    public function downloadTemplate()
    {
        $headers = [
            'nama', 'email', 'nik', 'telepon', 'jenis_kelamin',
            'tempat_lahir', 'tanggal_lahir', 'pendidikan', 'alamat', 'status'
        ];

        $contoh = [
            'Budi Santoso', 'budi@email.com', '3201010101010001',
            '081234567890', 'Laki-laki', 'Jakarta', '1995-08-17',
            'S1', 'Jl. Contoh No. 1', 'active'
        ];

        $filename = 'template_import_peserta.csv';
        $handle = fopen('php://output', 'w');

        return response()->stream(function () use ($handle, $headers, $contoh) {
            fputcsv($handle, $headers);
            fputcsv($handle, $contoh);
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
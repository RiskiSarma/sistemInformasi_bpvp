<?php 
namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use Illuminate\Validation\Rule;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $query = Instructor::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('expertise', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Order by name
        $instructors = $query->orderBy('name', 'asc')->paginate(12)->appends($request->all());
        
        return view('instructors.index', compact('instructors'));
    }

    public function create()
    {
        $users = User::where('role', 'instructor')
                    ->whereDoesntHave('instructor')
                    ->orderBy('name')
                    ->get();
                    
        return view('instructors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:instructors,user_id',
            'phone' => 'required|string|max:20',
            'expertise' => 'required|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $instructor = Instructor::create($validated + [
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Kirim notifikasi ke semua user yang punya role admin
        $admins = User::where('role', 'admin')->get(); // Sesuaikan dengan kolom role kamu
        // Jika pakai Spatie Permission: User::role('admin')->get();

        Notification::send($admins, new GeneralActivityNotification(
            $instructor,
            auth()->user(),
            'Instruktur',
            'ditambahkan'
        ));

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instruktur berhasil ditambahkan!');
    }

    public function show(Instructor $instructor)
    {
        // Load relasi dasar
        $instructor->load([
            'user',
            'programs.masterProgram',
            'programs.participants',
            'creator',
            'updater',
            'schedules' => fn($q) => $q->with('program.masterProgram')->where('is_active', true)
        ]);

        // Load schedules aktif beserta programnya
        $instructor->load([
            'schedules' => function ($query) {
                $query->with(['program.masterProgram'])
                    ->where('is_active', true);
            }
        ]);

        // Ambil program_id unik dari schedules aktif
        $programIds = $instructor->schedules->pluck('program_id')->unique();
        $programs = Program::whereIn('id', $programIds)->get();

        // Ambil data program untuk statistik (hanya yang ada di jadwal aktif)
        $programs = \App\Models\Program::whereIn('id', $programIds)->get();

        // Hitung statistik - SAMA PERSIS seperti di schedule()
        $totalPrograms = $programs->count();
        $ongoingPrograms = $programs->where('status', 'ongoing')->count();
        $plannedPrograms = $programs->where('status', 'planned')->count();
        $completedPrograms = $programs->where('status', 'completed')->count();

        return view('instructors.show', compact(
            'instructor',
            'totalPrograms',
            'ongoingPrograms',
            'plannedPrograms',
            'completedPrograms'
        ));
    }
    public function edit(Instructor $instructor)
    {
        $instructor->loadMissing('user');

        if (!$instructor->user) {
        return redirect()->route('admin.instructors.index')
            ->with('error', 'Data instruktur ini tidak terhubung dengan akun user. Hubungi developer untuk perbaikan data.');
    }

        return view('instructors.edit', compact('instructor'));
    }

    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email,' . $instructor->id,
            'phone' => 'required|string|max:20',
            'expertise' => 'required|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $instructor->update($validated + [
            'updated_by' => auth()->id(),
        ]);

        if ($instructor->user) {
        $instructor->user->updated_by = auth()->id();
        $instructor->user->touch();
        $instructor->user->save();
        }

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $instructor,
            auth()->user(),
            'Instruktur',
            'diperbarui'
        ));

        return redirect()->route('admin.instructors.index')
            ->with('success', 'Data instruktur berhasil diperbarui!');
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();
        
        return redirect()->route('admin.instructors.index')
            ->with('success', 'Instruktur berhasil dihapus!');
    }

    /**
 * Show instructor schedule
 */
public function schedule(Instructor $instructor) 
{
    // Load schedules dengan relasi program
    $instructor->load([
        'schedules' => function($query) {
            $query->with(['program.masterProgram', 'creator', 'updater'])
                  ->where('is_active', true)
                  ->orderBy('day_of_week')
                  ->orderBy('start_time');
        }
    ]);

    // PERBAIKAN: Ambil programs dari schedules yang ada
    // Karena schedules sudah terhubung ke program, kita bisa ambil dari sana
    $programIds = $instructor->schedules->pluck('program_id')->unique();
    
    // Sync instructor dengan programs yang ada di schedules
    // (Opsional - hanya jika Anda mau auto-sync)
    // $instructor->programs()->syncWithoutDetaching($programIds);
    
    // Ambil programs dengan status untuk statistik
    $programs = \App\Models\Program::whereIn('id', $programIds)->get();
    
    $totalPrograms = $programs->count();
    $ongoingPrograms = $programs->where('status', 'ongoing')->count();
    $plannedPrograms = $programs->where('status', 'planned')->count();
    $completedPrograms = $programs->where('status', 'completed')->count();

    // Organisir jadwal per hari
    $schedulesByDay = [
        'monday' => [],
        'tuesday' => [],
        'wednesday' => [],
        'thursday' => [],
        'friday' => [],
        'saturday' => [],
        'sunday' => [],
    ];

    foreach ($instructor->schedules as $schedule) {
        $schedulesByDay[$schedule->day_of_week][] = $schedule;
    }

    $days = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
    ];

    return view('instructors.schedule', compact(
        'instructor',
        'schedulesByDay',
        'days',
        'totalPrograms',
        'ongoingPrograms',
        'plannedPrograms',
        'completedPrograms'
    ));
}
public function assignProgramsForm(Instructor $instructor)
{
    // Ambil semua program yang tersedia
    $availablePrograms = Program::whereNotIn('id', $instructor->programs()->pluck('programs.id'))
        ->orderBy('name')
        ->get();
    
    // Ambil program yang sudah di-assign
    $assignedPrograms = $instructor->programs;
    
    return view('instructors.assign-programs', compact('instructor', 'availablePrograms', 'assignedPrograms'));
}

public function assignPrograms(Request $request, Instructor $instructor)
{
    $validated = $request->validate([
        'program_ids' => 'required|array',
        'program_ids.*' => 'exists:programs,id'
    ]);
    
    // Sync programs (akan replace yang lama dengan yang baru)
    $instructor->programs()->sync($validated['program_ids']);
    
    return redirect()->route('instructors.show', $instructor)
        ->with('success', 'Program berhasil di-assign ke instruktur!');
}
}
?>
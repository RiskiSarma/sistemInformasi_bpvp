<?php 
namespace App\Http\Controllers;

use App\Models\Instructor;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InstructorActivityNotification;
use App\Models\User;

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
        return view('instructors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:instructors,email',
            'phone' => 'required|string|max:20',
            'expertise' => 'required|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'education' => 'nullable|string',
            'certifications' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $instructor = Instructor::create($validated);

        // Kirim notifikasi ke semua user yang punya role admin
        $admins = User::where('role', 'admin')->get(); // Sesuaikan dengan kolom role kamu
        // Jika pakai Spatie Permission: User::role('admin')->get();

        Notification::send($admins, new InstructorActivityNotification(
            $instructor,
            Auth::user(),
            'ditambahkan'
        ));

        return redirect()->route(' instructors.index')
            ->with('success', 'Instruktur berhasil ditambahkan!');
    }

    public function show(Instructor $instructor)
    {
        $instructor->load('programs.masterProgram', 'creator', 'updater');
        
        return view('instructors.show', compact('instructor'));
    }

    public function edit(Instructor $instructor)
    {
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

        $instructor->update($validated);

        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new InstructorActivityNotification(
        $instructor,
        Auth::user(),
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
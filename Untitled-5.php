<?php 
namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Program;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::with('program.masterProgram');

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sorting functionality
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        
        // Validate sort field
        $allowedSortFields = ['id', 'name', 'email', 'status', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        $participants = $query->paginate(15)->appends($request->all());
        
        return view('participants.index', compact('participants', 'sortField', 'sortDirection'));
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
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'education' => 'nullable|string|max:100',
            'status' => 'required|in:active,graduated,dropout',
        ]);

        Participant::create($validated);

        return redirect()->route('participants.index')
            ->with('success', 'Peserta berhasil ditambahkan!');
    }

    public function show(Participant $participant)
    {
        $participant->load(['program.masterProgram', 'attendances']);
        
        // Calculate attendance statistics
        $totalAttendances = $participant->attendances->count();
        $presentCount = $participant->attendances->where('status', 'present')->count();
        $absentCount = $participant->attendances->where('status', 'absent')->count();
        $lateCount = $participant->attendances->where('status', 'late')->count();
        $excusedCount = $participant->attendances->where('status', 'excused')->count();
        
        $attendancePercentage = $totalAttendances > 0 
            ? round(($presentCount / $totalAttendances) * 100, 2) 
            : 0;

        return view('participants.show', compact(
            'participant', 
            'totalAttendances', 
            'presentCount', 
            'absentCount',
            'lateCount',
            'excusedCount',
            'attendancePercentage'
        ));
    }

    public function edit(Participant $participant)
    {
        $programs = Program::with('masterProgram')->get();
        return view('participants.edit', compact('participant', 'programs'));
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:participants,email,' . $participant->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'education' => 'nullable|string|max:100',
            'status' => 'required|in:active,graduated,dropout',
        ]);

        $participant->update($validated);

        return redirect()->route('participants.index')
            ->with('success', 'Data peserta berhasil diperbarui!');
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
        
        return redirect()->route('participants.index')
            ->with('success', 'Peserta berhasil dihapus!');
    }
}
?>
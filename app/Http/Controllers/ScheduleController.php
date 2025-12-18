<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Instructor;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ScheduleActivityNotification;
use App\Models\User;

class ScheduleController extends Controller
{
    /**
     * Show form to add schedule for instructor
     */
    public function create(Instructor $instructor)
    {
        // Get programs yang sedang berjalan atau akan datang
        $programs = Program::whereIn('status', ['planned', 'ongoing'])
            ->with('masterProgram')
            ->get();

        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return view('schedules.create', compact('instructor', 'programs', 'days'));
    }

    /**
     * Store schedule
     */
    public function store(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['instructor_id'] = $instructor->id;

        $schedule = Schedule::create($validated);
        // Check for time conflicts
        $conflict = Schedule::where('instructor_id', $instructor->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('is_active', true)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jadwal bentrok dengan jadwal lain pada hari dan jam yang sama!');
        }

        $schedule = Schedule::create([
        'instructor_id' => $instructor->id,
        'program_id' => $validated['program_id'],
        'day_of_week' => $validated['day_of_week'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'room' => $validated['room'] ?? null,
        'notes' => $validated['notes'] ?? null,
    ]);
        $program = Program::find($validated['program_id']);
        $program->instructor_id = $instructor->id;
        $program->save();

        // Kirim notifikasi
        $admins = User::where('role', 'admin')->get(); // Sesuaikan dengan sistem role kamu
        Notification::send($admins, new ScheduleActivityNotification($schedule, Auth::user(), 'ditambahkan'));
        
        return redirect()->route('instructors.schedule', $instructor)
            ->with('success', 'Jadwal mengajar berhasil ditambahkan!');
    }

    /**
     * Show edit form
     */
    public function edit(Schedule $schedule)
    {
        $instructor = $schedule->instructor;
        
        $programs = Program::whereIn('status', ['planned', 'ongoing'])
            ->with('masterProgram')
            ->get();

        $days = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        return view('schedules.edit', compact('schedule', 'instructor', 'programs', 'days'));
    }

    /**
     * Update schedule
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Check for time conflicts (exclude current schedule)
        $conflict = Schedule::where('instructor_id', $schedule->instructor_id)
            ->where('id', '!=', $schedule->id)
            ->where('day_of_week', $validated['day_of_week'])
            ->where('is_active', true)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Jadwal bentrok dengan jadwal lain pada hari dan jam yang sama!');
        }

        $schedule->update($validated);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new ScheduleActivityNotification($schedule, Auth::user(), 'diperbarui'));

        // OTOMATIS update instruktur di program jika program_id berubah
        $program = Program::find($validated['program_id']);
        $program->instructor_id = $schedule->instructor_id;
        $program->save();

        return redirect()->route('admin.instructors.schedule', $schedule->instructor)
            ->with('success', 'Jadwal mengajar berhasil diperbarui!');
    }

    /**
     * Delete schedule
     */
    public function destroy(Schedule $schedule)
    {
        $instructor = $schedule->instructor;
        $schedule->delete();

        return redirect()->route('instructors.schedule', $instructor)
            ->with('success', 'Jadwal mengajar berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PengajarEksternal;
use App\Models\Schedule;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajarEksternalScheduleController extends Controller
{
    /**
     * Show form to create schedule
     */
    public function create(PengajarEksternal $pengajarEksternal)
    {
        $programs = Program::with('masterProgram')
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
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

        return view('pengajar-eksternal.schedules.create', compact('pengajarEksternal', 'programs', 'days'));
    }

    /**
     * Store new schedule
     */
    public function store(Request $request, PengajarEksternal $pengajarEksternal)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'program_id.required' => 'Program harus dipilih',
            'day_of_week.required' => 'Hari harus dipilih',
            'start_time.required' => 'Waktu mulai harus diisi',
            'end_time.required' => 'Waktu selesai harus diisi',
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai',
        ]);

        try {
            DB::beginTransaction();

            // Check for schedule conflicts
            $conflict = Schedule::where('pengajar_eksternal_id', $pengajarEksternal->id)
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
                return back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal yang sudah ada!');
            }

            Schedule::create([
                'pengajar_eksternal_id' => $pengajarEksternal->id,
                'instructor_id'         => null,
                'program_id' => $validated['program_id'],
                'day_of_week' => $validated['day_of_week'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'room' => $validated['room'],
                'notes' => $validated['notes'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()->route('admin.pengajar-eksternal.schedule', $pengajarEksternal)
                ->with('success', 'Jadwal berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit schedule
     */
    public function edit(Schedule $schedule)
    {
        // Pastikan schedule milik pengajar eksternal
        if (!$schedule->pengajar_eksternal_id) {
            return redirect()->back()->with('error', 'Jadwal ini bukan milik pengajar eksternal!');
        }

        $schedule->load(['pengajarEksternal', 'program.masterProgram']);

        $programs = Program::with('masterProgram')
            ->where('status', '!=', 'completed')
            ->orderBy('created_at', 'desc')
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

        return view('pengajar-eksternal.schedules.edit', compact('schedule', 'programs', 'days'));
    }

    /**
     * Update schedule
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Pastikan schedule milik pengajar eksternal
        if (!$schedule->pengajar_eksternal_id) {
            return redirect()->back()->with('error', 'Jadwal ini bukan milik pengajar eksternal!');
        }

        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'program_id.required' => 'Program harus dipilih',
            'day_of_week.required' => 'Hari harus dipilih',
            'start_time.required' => 'Waktu mulai harus diisi',
            'end_time.required' => 'Waktu selesai harus diisi',
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai',
        ]);

        try {
            DB::beginTransaction();

            // Check for conflicts (exclude current schedule)
            $conflict = Schedule::where('pengajar_eksternal_id', $schedule->pengajar_eksternal_id)
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
                return back()->withInput()->with('error', 'Jadwal bentrok dengan jadwal yang sudah ada!');
            }

            $schedule->update($validated);

            DB::commit();

            return redirect()->route('admin.pengajar-eksternal.schedule', $schedule->pengajarEksternal)
                ->with('success', 'Jadwal berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Delete schedule
     */
    public function destroy(Schedule $schedule)
    {
        // Pastikan schedule milik pengajar eksternal
        if (!$schedule->pengajar_eksternal_id) {
            return redirect()->back()->with('error', 'Jadwal ini bukan milik pengajar eksternal!');
        }

        try {
            $pengajarEksternal = $schedule->pengajarEksternal;
            $schedule->delete();

            return redirect()->route('admin.pengajar-eksternal.schedule', $pengajarEksternal)
                ->with('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
        }
    }
}
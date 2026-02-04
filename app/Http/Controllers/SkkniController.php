<?php

namespace App\Http\Controllers;

use App\Models\Skkni;
use App\Models\IndependentCompetencyUnit;
use App\Models\MasterProgram;
use App\Models\ProgramPelatihanUnits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use App\Models\User;

class SkkniController extends Controller
{
    /**
     * Display a listing of SKKNI
     */
    public function index(Request $request)
    {
        $query = Skkni::with('independentUnits');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                  ->orWhere('skkni', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('berlaku')) {
            $query->where('berlaku', $request->berlaku);
        }

        $skknis = $query->latest()->paginate(15);

        return view('programs.skkni.index', compact('skknis'));
    }

    /**
     * Sync SKKNI from Proglat API using Artisan Command
     */
    public function syncFromProglat()
    {
        try {
            // Jalankan command artisan untuk sync
            Artisan::call('proglat:sync-skkni', [
                '--limit' => 100,
                '--page' => 1,
            ]);

            $output = Artisan::output();

            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new GeneralActivityNotification(
                null,
                auth()->user(),
                'SKKNI',
                'disinkronisasi dari Proglat'
            ));

            return redirect()->route('admin.skkni.index')
                ->with('success', 'Sinkronisasi SKKNI berhasil! ' . trim($output));

        } catch (\Exception $e) {
            Log::error('SKKNI Sync via Controller Failed', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.skkni.index')
                ->with('error', 'Gagal sinkronisasi SKKNI: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified SKKNI with its units
     */
    public function show(Skkni $skkni)
    {
        $skkni->load('independentUnits.programPelatihanUnits.masterProgram');
        $masterPrograms = MasterProgram::where('is_active', true)->get();

        return view('programs.skkni.show', compact('skkni', 'masterPrograms'));
    }

    /**
     * Store a new unit under this SKKNI
     */
    public function storeUnit(Request $request, Skkni $skkni)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:independent_competency_units,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program_pelatihan_id' => 'nullable|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        // Create unit
        $unit = IndependentCompetencyUnit::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'skkni_id' => $skkni->id,
        ]);

        // Create pivot if program selected
        if ($request->filled('program_pelatihan_id')) {
            ProgramPelatihanUnits::create([
                'program_pelatihan_id' => $request->program_pelatihan_id,
                'master_programs_id' => $request->program_pelatihan_id,
                'independent_competency_units_id' => $unit->id,
                'type_unit' => 'skkni',
                'jp' => $request->jp ?? null,
                'sub_unit_kompetensi' => 'N',
            ]);
        }

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            auth()->user(),
            'Unit Kompetensi',
            'ditambahkan ke SKKNI: ' . $skkni->nomor
        ));

        return redirect()->route('admin.skkni.show', $skkni)
                         ->with('success', 'Unit kompetensi berhasil ditambahkan!');
    }

    /**
     * Update the specified unit
     */
    public function updateUnit(Request $request, $unitId)
    {
        $unit = IndependentCompetencyUnit::findOrFail($unitId);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:independent_competency_units,code,' . $unit->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $unit->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            auth()->user(),
            'Unit Kompetensi',
            'diperbarui'
        ));

        return redirect()->route('admin.skkni.show', $unit->skkni_id)
                         ->with('success', 'Unit kompetensi berhasil diperbarui!');
    }

    /**
     * Remove the specified unit
     */
    public function destroyUnit($unitId)
    {
        $unit = IndependentCompetencyUnit::findOrFail($unitId);
        $skkniId = $unit->skkni_id;
        
        // Delete pivot records first
        ProgramPelatihanUnits::where('independent_competency_units_id', $unit->id)->delete();
        
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            auth()->user(),
            'Unit Kompetensi',
            'dihapus'
        ));

        // Delete unit
        $unit->delete();

        return redirect()->route('admin.skkni.show', $skkniId)
                         ->with('success', 'Unit kompetensi berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\IndependentCompetencyUnit;
use App\Models\ProgramPelatihanUnits;
use App\Models\Skkni;
use App\Models\MasterProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class IndependentUnitController extends Controller
{
    /**
     * Display a listing of SKKNI
     */
    public function index(Request $request)
    {
        $query = Skkni::with('independentUnits');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor', 'like', "%{$search}%")
                  ->orWhere('skkni', 'like', "%{$search}%");
            });
        }

        if ($request->filled('berlaku')) {
            $query->where('berlaku', $request->berlaku);
        }

        $skknis = $query->orderByRaw('YEAR(tanggal) DESC, CAST(SUBSTRING_INDEX(nomor, " ", -1) AS UNSIGNED) DESC')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

        return view('programs.independent-units.index', compact('skknis'));
    }

    /**
     * Sync SKKNI from Proglat
     */
    public function syncProglat(Request $request)
    {
        try {
            Artisan::call('proglat:sync-skkni', [
                '--limit' => 100,
                '--page'  => 1,
            ]);

            return redirect()
                ->route('admin.independent-units.index')
                ->with('success', 'Sinkronisasi SKKNI dari Proglat berhasil dilakukan!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal sinkronisasi SKKNI: ' . $e->getMessage());
        }
    }

    /**
     * Show SKKNI detail with its units
     */
    public function show($id)
    {
        $skkni = Skkni::with('independentUnits.programPelatihanUnits.masterProgram')->findOrFail($id);
        $masterPrograms = MasterProgram::where('is_active', true)->get();

        return view('programs.independent-units.show', compact('skkni', 'masterPrograms'));
    }

    /**
     * Preview file PDF dari Proglat (proxy)
     */
    public function previewFile(Skkni $skkni)
    {
        if (!$skkni->file_path) {
            abort(404, 'File tidak tersedia');
        }

        // Cek apakah file lokal atau remote
        if (str_starts_with($skkni->file_path, 'skkni/')) {
            // File lokal
            $path = storage_path('app/public/' . $skkni->file_path);
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan');
            }
            return response()->file($path);
        }

        // File remote - proxy dari Proglat
        try {
            $possibleUrls = [
                'https://skkni-api.kemnaker.go.id/storage/' . $skkni->file_path,
                'https://skkni-api.kemnaker.go.id/' . $skkni->file_path,
            ];

            foreach ($possibleUrls as $url) {
                try {
                    $response = Http::timeout(30)->get($url);
                    
                    if ($response->successful() && $response->header('Content-Type') === 'application/pdf') {
                        return response($response->body(), 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline; filename="' . ($skkni->file_name ?? 'document.pdf') . '"',
                        ]);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            abort(404, 'File tidak dapat diakses dari server Proglat');

        } catch (\Exception $e) {
            Log::error('File preview failed', [
                'skkni' => $skkni->nomor,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Gagal mengambil file: ' . $e->getMessage());
        }
    }

    /**
     * Download file PDF dari Proglat (proxy)
     */
    public function downloadFile(Skkni $skkni)
    {
        if (!$skkni->file_path) {
            abort(404, 'File tidak tersedia');
        }

        // Cek apakah file lokal atau remote
        if (str_starts_with($skkni->file_path, 'skkni/')) {
            // File lokal
            $path = storage_path('app/public/' . $skkni->file_path);
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan');
            }
            return response()->download($path, $skkni->file_name ?? 'document.pdf');
        }

        // File remote - download dari Proglat
        try {
            $possibleUrls = [
                'https://skkni-api.kemnaker.go.id/storage/' . $skkni->file_path,
                'https://skkni-api.kemnaker.go.id/' . $skkni->file_path,
            ];

            foreach ($possibleUrls as $url) {
                try {
                    $response = Http::timeout(30)->get($url);
                    
                    if ($response->successful() && $response->header('Content-Type') === 'application/pdf') {
                        return response($response->body(), 200, [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'attachment; filename="' . ($skkni->file_name ?? 'document.pdf') . '"',
                        ]);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            abort(404, 'File tidak dapat diakses dari server Proglat');

        } catch (\Exception $e) {
            Log::error('File download failed', [
                'skkni' => $skkni->nomor,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Gagal mendownload file: ' . $e->getMessage());
        }
    }

    /**
     * Store a new unit under SKKNI
     */
    public function storeUnit(Request $request, $skkniId)
    {
        $skkni = Skkni::findOrFail($skkniId);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:independent_competency_units,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'program_pelatihan_id' => 'nullable|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        $unit = IndependentCompetencyUnit::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'skkni_id' => $skkni->id,
        ]);

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

        return redirect()->route('admin.independent-units.show', $skkni->id)
                         ->with('success', 'Unit kompetensi berhasil ditambahkan!');
    }

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

        return redirect()->route('admin.independent-units.show', $unit->skkni_id)
                         ->with('success', 'Unit kompetensi berhasil diperbarui!');
    }

    public function destroyUnit($unitId)
    {
        $unit = IndependentCompetencyUnit::findOrFail($unitId);
        $skkniId = $unit->skkni_id;
        
        ProgramPelatihanUnits::where('independent_competency_units_id', $unit->id)->delete();
        
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $unit,
            auth()->user(),
            'Unit Kompetensi',
            'dihapus'
        ));

        $unit->delete();

        return redirect()->route('admin.independent-units.show', $skkniId)
                         ->with('success', 'Unit kompetensi berhasil dihapus!');
    }

    public function create()
    {
        $skknis = Skkni::all();
        $masterPrograms = MasterProgram::all();
        return view('programs.independent-units.create', compact('skknis', 'masterPrograms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:independent_competency_units,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skkni_id' => 'required|exists:skknis,id',
            'program_pelatihan_id' => 'nullable|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        $unitData = [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'skkni_id' => $validated['skkni_id'],
        ];
        
        $unit = IndependentCompetencyUnit::create($unitData);

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

        return redirect()->route('admin.independent-units.index')
                         ->with('success', 'Unit kompetensi berhasil dibuat!');
    }

    public function storeSkkni(Request $request)
    {
        $validated = $request->validate([
            'skkni_name' => 'required|string|max:255',
            'nomor' => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'berlaku' => 'nullable|in:Y,N',
        ]);

        $skkni = Skkni::create([
            'skkni' => $validated['skkni_name'],
            'nomor' => $validated['nomor'],
            'tanggal' => $validated['tanggal'] ?? null,
            'berlaku' => $validated['berlaku'] ?? 'Y',
        ]);

        return response()->json([
            'success' => true,
            'skkni' => $skkni,
            'message' => 'SKKNI berhasil ditambahkan!'
        ]);
    }

    public function edit(IndependentCompetencyUnit $unit)
    {
        $unit->load(['skkni', 'programPelatihanUnits' => function($q) {
            $q->with('masterProgram');
        }]);
        $skknis = Skkni::all();
        $masterPrograms = MasterProgram::all();
        return view('programs.independent-units.edit', compact('unit', 'skknis', 'masterPrograms'));
    }

    public function update(Request $request, IndependentCompetencyUnit $unit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:independent_competency_units,code,' . $unit->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'skkni_id' => 'required|exists:skknis,id',
            'program_pelatihan_id' => 'nullable|exists:master_programs,id',
            'jp' => 'nullable|integer|min:0',
        ]);

        $unitData = [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'skkni_id' => $validated['skkni_id'],
        ];
        
        $unit->update($unitData);

        if ($request->filled('program_pelatihan_id')) {
            ProgramPelatihanUnits::updateOrCreate(
                [
                    'independent_competency_units_id' => $unit->id,
                    'program_pelatihan_id' => $request->program_pelatihan_id,
                ],
                [
                    'master_programs_id' => $request->program_pelatihan_id,
                    'type_unit' => 'skkni',
                    'jp' => $request->jp ?? null,
                    'sub_unit_kompetensi' => 'N',
                ]
            );
        } else {
            ProgramPelatihanUnits::where('independent_competency_units_id', $unit->id)->delete();
        }

        return redirect()->route('admin.independent-units.index')
                         ->with('success', 'Unit kompetensi berhasil diperbarui!');
    }

    public function destroy(IndependentCompetencyUnit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.independent-units.index')
                         ->with('success', 'Unit kompetensi berhasil dihapus!');
    }

    public function updateSkkni(Request $request, Skkni $skkni)
    {
        $validated = $request->validate([
            'nomor'   => 'required|string|max:100',
            'skkni'   => 'required|string|max:255',
            'tanggal' => 'nullable|date',
            'berlaku' => 'required|in:Y,N',
        ]);

        $skkni->update($validated);

        return redirect()->route('admin.independent-units.index')
                         ->with('success', 'SKKNI berhasil diperbarui!');
    }

    public function destroySkkni(Skkni $skkni)
    {
        IndependentCompetencyUnit::where('skkni_id', $skkni->id)->delete();
        ProgramPelatihanUnits::where('skkni_id', $skkni->id)->delete();
        $skkni->delete();

        return redirect()->route('admin.independent-units.index')
                         ->with('success', 'SKKNI dan unit terkait berhasil dihapus!');
    }
}
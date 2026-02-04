<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihan;
use App\Models\JenisPelatihan;
use App\Models\MasterProgram;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class PaketPelatihanController extends Controller
{
    public function index()
    {
        $pakets = PaketPelatihan::with([
            'jenisPelatihan',
            'user',
            'programs.masterProgram',
        ])->latest()->paginate(10);

        $jenisPelatihans = JenisPelatihan::all();
        $masterPrograms = MasterProgram::all();
        $programPelatihanUnits = \App\Models\ProgramPelatihanUnits::with('independentCompetencyUnit')->get();
        $masterProgramSubUnits = \App\Models\MasterProgram::all(); // Load untuk dropdown
        $allCompetencyUnits = \App\Models\IndependentCompetencyUnit::select('id', 'name', 'code')
            ->orderBy('name')
            ->get();
        $users = User::all();

        return view('paket-pelatihan.index', compact(
            'pakets',
            'jenisPelatihans',
            'masterPrograms',
            'programPelatihanUnits',
            'masterProgramSubUnits',
            'allCompetencyUnits',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'batch' => 'nullable|integer',
            'jp_harian' => 'nullable|integer|min:0',
            'sabtu_masuk' => 'required|in:Y,N',
            'minggu_masuk' => 'required|in:Y,N',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date',
            'tanggal_awal_pendaftaran' => 'nullable|date',
            'tanggal_akhir_pendaftaran' => 'nullable|date|after_or_equal:tanggal_awal_pendaftaran',
            'tanggal_awal_tes_tulis' => 'nullable|date',
            'tanggal_akhir_tes_tulis' => 'nullable|date|after_or_equal:tanggal_awal_tes_tulis',
            'tanggal_awal_wawancara' => 'nullable|date',
            'tanggal_akhir_wawancara' => 'nullable|date|after_or_equal:tanggal_awal_wawancara',
            'tanggal_awal_daftar_ulang' => 'nullable|date',
            'tanggal_akhir_daftar_ulang' => 'nullable|date|after_or_equal:tanggal_awal_daftar_ulang',
            'tanggal_pengumuman' => 'nullable|date',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'exists:master_programs,id',
        ]);

        $validated['user_id'] = auth()->id();

        $paket = PaketPelatihan::create($validated);

        // Update program dengan paket_pelatihan_id
        if ($request->filled('program_ids')) {
            foreach ($request->program_ids as $programId) {
                Program::where('id', $programId)->update([
                    'paket_pelatihan_id' => $paket->id,
                ]);
            }
        }

        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil ditambahkan!');
    }

    public function edit(PaketPelatihan $paketPelatihan)
    {
        $paketPelatihan->load('programs');
        $jenisPelatihans = JenisPelatihan::all();
        $masterPrograms = MasterProgram::all();
        $users = User::all();
        return view('programs.paket-pelatihan.edit', compact('paketPelatihan', 'jenisPelatihans', 'masterPrograms', 'users'));
    }

    public function update(Request $request, PaketPelatihan $paketPelatihan)
    {
        $validated = $request->validate([
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'batch' => 'nullable|integer',
            'jp_harian' => 'nullable|integer|min:0',
            'sabtu_masuk' => 'required|in:Y,N',
            'minggu_masuk' => 'required|in:Y,N',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date',
            'tanggal_awal_pendaftaran' => 'nullable|date',
            'tanggal_akhir_pendaftaran' => 'nullable|date|after_or_equal:tanggal_awal_pendaftaran',
            'tanggal_awal_tes_tulis' => 'nullable|date',
            'tanggal_akhir_tes_tulis' => 'nullable|date|after_or_equal:tanggal_awal_tes_tulis',
            'tanggal_awal_wawancara' => 'nullable|date',
            'tanggal_akhir_wawancara' => 'nullable|date|after_or_equal:tanggal_awal_wawancara',
            'tanggal_awal_daftar_ulang' => 'nullable|date',
            'tanggal_akhir_daftar_ulang' => 'nullable|date|after_or_equal:tanggal_awal_daftar_ulang',
            'tanggal_pengumuman' => 'nullable|date',
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'exists:master_programs,id',
        ]);

        $paketPelatihan->update($validated);

        // Lepaskan program yang tidak dipilih
        Program::where('paket_pelatihan_id', $paketPelatihan->id)
                ->whereNotIn('id', $request->program_ids ?? [])
                ->update(['paket_pelatihan_id' => null]);

        // Update program yang dipilih
        if ($request->filled('program_ids')) {
            foreach ($request->program_ids as $programId) {
                Program::where('id', $programId)->update([
                    'paket_pelatihan_id' => $paketPelatihan->id,
                ]);
            }
        }

        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil diperbarui!');
    }

    public function destroy(PaketPelatihan $paketPelatihan)
    {
        // Lepaskan relasi program sebelum delete
        Program::where('paket_pelatihan_id', $paketPelatihan->id)
                ->update(['paket_pelatihan_id' => null]);
        
        // Hapus paket pelatihan
        $paketPelatihan->delete();
        
        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil dihapus!');
    }
}
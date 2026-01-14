<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\MasterProgram;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with('masterProgram', 'jenisPelatihan')->paginate(15);
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        $jenisPelatihans = JenisPelatihan::all();
        return view('batches.index', compact('batches', 'masterPrograms', 'jenisPelatihans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:batches',
            'name' => 'required|string|max:255',
            'master_program_id' => 'required|exists:master_programs,id',
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            // 'jenis_pelatihan' => 'required|string|in:Non Boarding,Boarding,Project Based Learning (PBL),Tailor Made Training,PFLK',
            'is_active' => 'boolean',
        ]);

        Batch::create($validated);

        return redirect()->route('admin.programs.batches.index')
            ->with('success', 'Batch berhasil ditambahkan!');
    }

    public function edit(Batch $batch)
    {
        $batch->load(['masterProgram', 'jenisPelatihan']);
        $masterPrograms = MasterProgram::where('is_active', true)->get();
        $jenisPelatihans = JenisPelatihan::all();
        return view('batches.edit', compact('batches', 'masterPrograms', 'jenisPelatihans'));
    }

    public function update(Request $request, Batch $batch)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:batches,code,' . $batch->id,
            'name' => 'required|string|max:255',
            'master_program_id' => 'required|exists:master_programs,id',
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            // 'jenis_pelatihan' => 'required|string|in:Non Boarding,Boarding,Project Based Learning (PBL),Tailor Made Training,PFLK',
            'is_active' => 'boolean',
        ]);

        $batch->update($validated);

        return redirect()->route('admin.programs.batches.index')
            ->with('success', 'Batch berhasil diperbarui!');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();
        return redirect()->route('admin.programs.batches.index')
            ->with('success', 'Batch berhasil dihapus!');
    }
}
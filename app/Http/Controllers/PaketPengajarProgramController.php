<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihanPengajarProgram;
use App\Models\Program;
use Illuminate\Http\Request;

class PaketPengajarProgramController extends Controller
{
    public function index(Program $program)
    {
        $pengajars = $program->paketPelatihanPengajarPrograms()->paginate(10);
        return view('programs.paket-pengajar-programs.index', compact('program', 'pengajars'));
    }

    public function create(Program $program)
    {
        return view('programs.paket-pengajar-programs.create', compact('program'));
    }

    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'paket_pelatihan_program_id' => 'required|exists:paket_pelatihan_programs,id',
            'jenis_materi_pelatihan_id' => 'required|exists:jenis_materi_pelatihans,id',
            'pengajar_eksternal' => 'required|in:Y,N',
            'pengajar_internal_id' => 'nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'nullable|exists:pengajar_eksternals,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $program->paketPelatihanPengajarPrograms()->create($validated);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Paket pengajar program berhasil ditambahkan!');
    }

    // Edit, update, destroy mirip
}
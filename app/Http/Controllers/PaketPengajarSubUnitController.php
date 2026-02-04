<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihanPengajarSubUnit;
use App\Models\Program;
use Illuminate\Http\Request;

class PaketPengajarSubUnitController extends Controller
{
    public function index(Program $program)
    {
        $pengajars = $program->paketPelatihanPengajarSubUnits()->paginate(10);
        return view('programs.paket-pengajar-sub-units.index', compact('program', 'pengajars'));
    }

    public function create(Program $program)
    {
        return view('programs.paket-pengajar-sub-units.create', compact('program'));
    }

    public function store(Request $request, Program $program)
    {
        $validated = $request->validate([
            'paket_pelatihan_unit_id' => 'required|exists:paket_pelatihan_units,id',
            'paket_pelatihan_program_id' => 'required|exists:paket_pelatihan_programs,id',
            'pengajar_internal_id' => 'nullable|exists:instructors,id',
            'pengajar_eksternal_id' => 'nullable|exists:pengajar_eksternals,id',
            'pengajar_eksternal' => 'required|in:Y,N',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $program->paketPelatihanPengajarSubUnits()->create($validated);

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'Paket pengajar sub unit berhasil ditambahkan!');
    }

    // Edit, update, destroy mirip
}
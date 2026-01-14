<?php

namespace App\Http\Controllers;

use App\Models\JenisPelatihan;
use Illuminate\Http\Request;

class JenisPelatihanController extends Controller
{
    public function index()
    {
        $jenis = JenisPelatihan::latest()->paginate(10);
        return view('jenis-pelatihan.index', compact('jenis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pelatihan' => 'required|string|max:100|in:Non Boarding,Boarding,Project Based Learning (PBL),Tailor Made Training,PFLK',
        ]);

        JenisPelatihan::create([
            'jenis_pelatihan' => $validated['jenis_pelatihan'],
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.programs.jenis-pelatihan.index')
                         ->with('success', 'Jenis pelatihan berhasil ditambahkan!');
    }

    public function update(Request $request, JenisPelatihan $jenis)
{
    $validated = $request->validate([
        'jenis_pelatihan' => 'required|string|max:100|in:Non Boarding,Boarding,Project Based Learning (PBL),Tailor Made Training,PFLK',
    ]);

    // Paksa update tanpa dirty checking
    $jenis->jenis_pelatihan = $validated['jenis_pelatihan'];
    $jenis->save(); // atau $jenis->update($validated) tetap OK, tapi save lebih aman

    return redirect()->route('admin.programs.jenis-pelatihan.index')
                     ->with('success', 'Jenis pelatihan berhasil diperbarui!');
}

    public function destroy(JenisPelatihan $jenisPelatihan)
    {
        $jenisPelatihan->delete();
        return redirect()->route('admin.programs.jenis-pelatihan.index')
                         ->with('success', 'Jenis pelatihan berhasil dihapus!');
    }
}
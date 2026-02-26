<?php

namespace App\Http\Controllers;

use App\Models\JenisMateriPelatihan;
use Illuminate\Http\Request;

class JenisMateriPelatihanController extends Controller
{
    public function index()
    {
        // ✅ FIX: Load relasi user
        $jenisMateri = JenisMateriPelatihan::with('user')
            ->orderBy('jenis_materi_pelatihan', 'asc')
            ->get();
        
        return view('jenis-materi-pelatihan.index', compact('jenisMateri'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_materi_pelatihan' => 'required|string|max:255|unique:jenis_materi_pelatihan,jenis_materi_pelatihan',
        ], [
            'jenis_materi_pelatihan.required' => 'Jenis materi pelatihan harus diisi',
            'jenis_materi_pelatihan.unique' => 'Jenis materi pelatihan sudah ada',
        ]);

        JenisMateriPelatihan::create($validated);

        return redirect()->route('admin.jenis-materi-pelatihan.index')
            ->with('success', 'Jenis materi pelatihan berhasil ditambahkan!');
    }

    public function update(Request $request, JenisMateriPelatihan $jenisMateriPelatihan)
    {
        $validated = $request->validate([
            'jenis_materi_pelatihan' => 'required|string|max:255|unique:jenis_materi_pelatihan,jenis_materi_pelatihan,' . $jenisMateriPelatihan->id,
        ], [
            'jenis_materi_pelatihan.required' => 'Jenis materi pelatihan harus diisi',
            'jenis_materi_pelatihan.unique' => 'Jenis materi pelatihan sudah ada',
        ]);

        $jenisMateriPelatihan->update($validated);

        return redirect()->route('admin.jenis-materi-pelatihan.index')
            ->with('success', 'Jenis materi pelatihan berhasil diperbarui!');
    }

    public function destroy(JenisMateriPelatihan $jenisMateriPelatihan)
    {
        $jenisMateriPelatihan->delete();

        return redirect()->route('admin.jenis-materi-pelatihan.index')
            ->with('success', 'Jenis materi pelatihan berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PaketPelatihan;
use App\Models\JenisPelatihan;
use Illuminate\Http\Request;

class PaketPelatihanController extends Controller
{
    public function index()
    {
        $pakets = PaketPelatihan::with('jenisPelatihan', 'user')->latest()->paginate(10);
        $jenisPelatihans = JenisPelatihan::all();
        return view('paket-pelatihan.index', compact('pakets', 'jenisPelatihans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'batch' => 'nullable|integer',
            'jp_harian' => 'nullable|integer|min:0',
            'jp_industri' => 'nullable|integer|min:0',
            'sabtu_masuk' => 'required|in:Y,N',
            'minggu_masuk' => 'required|in:Y,N',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanggal_awal_pendaftaran' => 'nullable|date',
            'tanggal_akhir_pendaftaran' => 'nullable|date|after_or_equal:tanggal_awal_pendaftaran',
            'tanggal_awal_tes_tulis' => 'nullable|date',
            'tanggal_akhir_tes_tulis' => 'nullable|date|after_or_equal:tanggal_awal_tes_tulis',
            'tanggal_awal_wawancara' => 'nullable|date',
            'tanggal_akhir_wawancara' => 'nullable|date|after_or_equal:tanggal_awal_wawancara',
            'tanggal_awal_daftar_ulang' => 'nullable|date',
            'tanggal_akhir_daftar_ulang' => 'nullable|date|after_or_equal:tanggal_awal_daftar_ulang',
            'tanggal_pengumuman' => 'nullable|date',
            'user_id_pengumuman' => 'nullable|exists:users,id',
        ]);

        $validated['user_id'] = auth()->id();

        PaketPelatihan::create($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil ditambahkan!');
    }

    public function update(Request $request, PaketPelatihan $paketPelatihan)
    {
        $validated = $request->validate([
            'jenis_pelatihan_id' => 'required|exists:jenis_pelatihans,id',
            'tahun' => 'required|integer|min:2000|max:2100',
            'batch' => 'nullable|integer',
            'jp_harian' => 'nullable|integer|min:0',
            'jp_industri' => 'nullable|integer|min:0',
            'sabtu_masuk' => 'required|in:Y,N',
            'minggu_masuk' => 'required|in:Y,N',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tanggal_awal_pendaftaran' => 'nullable|date',
            'tanggal_akhir_pendaftaran' => 'nullable|date|after_or_equal:tanggal_awal_pendaftaran',
            'tanggal_awal_tes_tulis' => 'nullable|date',
            'tanggal_akhir_tes_tulis' => 'nullable|date|after_or_equal:tanggal_awal_tes_tulis',
            'tanggal_awal_wawancara' => 'nullable|date',
            'tanggal_akhir_wawancara' => 'nullable|date|after_or_equal:tanggal_awal_wawancara',
            'tanggal_awal_daftar_ulang' => 'nullable|date',
            'tanggal_akhir_daftar_ulang' => 'nullable|date|after_or_equal:tanggal_awal_daftar_ulang',
            'tanggal_pengumuman' => 'nullable|date',
            'user_id_pengumuman' => 'nullable|exists:users,id',
        ]);

        $paketPelatihan->update($validated);

        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil diperbarui!');
    }

    public function destroy(PaketPelatihan $paketPelatihan)
    {
        $paketPelatihan->delete();
        return redirect()->route('admin.programs.paket-pelatihan.index')
                         ->with('success', 'Paket pelatihan berhasil dihapus!');
    }
}
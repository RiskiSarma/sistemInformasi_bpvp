<?php

namespace App\Http\Controllers;

use App\Models\BidangPelatihan;
use App\Models\Kejuruan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use App\Models\User;

class KejuruanBidangController extends Controller
{
    /**
     * Display the combined Kejuruan & Bidang page with tabs.
     */
    public function index()
    {
        $kejuruans = Kejuruan::all();
        $bidangs   = BidangPelatihan::all();

        return view('kejuruan-bidang.index', compact('kejuruans', 'bidangs'));
    }

    /**
     * Sync Kejuruan from Proglat API
     */
    public function syncKejuruan()
    {
        try {
            // Run sync command
            $exitCode = Artisan::call('proglat:sync-kejuruan', [
                '--limit' => 100
            ]);

            // Hitung jumlah kejuruan setelah sync
            $totalKejuruan = Kejuruan::count();

            // Notifikasi admin yang lebih clean
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new GeneralActivityNotification(
                null,
                auth()->user(),
                'Kejuruan',
                'disinkronkan dari Proglat'
            ));

            return redirect()
                ->route('admin.programs.kejuruan-bidang.index')
                ->with('success', "Sinkronisasi kejuruan berhasil! Total {$totalKejuruan} kejuruan tersimpan di database.");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal sinkronisasi kejuruan: ' . $e->getMessage());
        }
    }
    /**
     * Store a new Kejuruan.
     */
    public function storeKejuruan(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:kejuruans,code',
            'kejuruan' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        Kejuruan::create($validated);

        return redirect()->route('admin.programs.kejuruan-bidang.index')
            ->with('success', 'Kejuruan berhasil ditambahkan.');
    }

    /**
     * Update an existing Kejuruan.
     */
    public function updateKejuruan(Request $request, Kejuruan $kejuruan)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:kejuruans,code',
            'kejuruan' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
        ]);

        $kejuruan->update($validated);

        return redirect()->route('admin.programs.kejuruan-bidang.index')
            ->with('success', 'Kejuruan berhasil diperbarui.');
    }

    /**
     * Delete a Kejuruan.
     */
    public function destroyKejuruan(Kejuruan $kejuruan)
    {
        try {
            // Check if kejuruan is being used
            if ($kejuruan->masterPrograms()->count() > 0) {
                return redirect()->route('admin.programs.kejuruan-bidang.index')
                    ->with('error', 'Kejuruan tidak dapat dihapus karena masih digunakan oleh program pelatihan!');
            }

            $kejuruan->delete();

            return redirect()->route('admin.programs.kejuruan-bidang.index')
                ->with('success', 'Kejuruan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting kejuruan', ['error' => $e->getMessage()]);
            return redirect()->route('admin.programs.kejuruan-bidang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus kejuruan!');
        }
    }

    /**
     * Store a new Bidang Pelatihan.
     */
    public function storeBidang(Request $request)
    {
        $validated = $request->validate([
            // 'kejuruan_id'        => 'required|exists:kejuruans,id',
            'bidang_pelatihan'   => 'required|string|max:100|unique:bidang_pelatihans,bidang_pelatihan',
            'deskripsi'          => 'nullable|string',
        ]);

        BidangPelatihan::create($validated);

        return redirect()->route('admin.programs.kejuruan-bidang.index')
            ->with('success', 'Bidang Pelatihan berhasil ditambahkan.');
    }

    /**
     * Update an existing Bidang Pelatihan.
     */
    public function updateBidang(Request $request, BidangPelatihan $bidang)
    {
        $validated = $request->validate([
            // 'kejuruan_id'        => 'required|exists:kejuruans,id',
            'bidang_pelatihan'   => 'required|string|max:100|unique:bidang_pelatihans,bidang_pelatihan,' . $bidang->id,
            'deskripsi'          => 'nullable|string',
        ]);

        $bidang->update($validated);

        return redirect()->route('admin.programs.kejuruan-bidang.index')
            ->with('success', 'Bidang Pelatihan berhasil diperbarui.');
    }

    /**
     * Delete a Bidang Pelatihan.
     */
    public function destroyBidang(BidangPelatihan $bidang)
    {
        try {
            // Check if bidang is being used
            if ($bidang->masterPrograms()->count() > 0) {
                return redirect()->route('admin.programs.kejuruan-bidang.index')
                    ->with('error', 'Bidang pelatihan tidak dapat dihapus karena masih digunakan oleh program pelatihan!');
            }

            $bidang->delete();

            return redirect()->route('admin.programs.kejuruan-bidang.index')
                ->with('success', 'Bidang pelatihan berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('Error deleting bidang', ['error' => $e->getMessage()]);
            return redirect()->route('admin.programs.kejuruan-bidang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus bidang pelatihan!');
        }
    }
}
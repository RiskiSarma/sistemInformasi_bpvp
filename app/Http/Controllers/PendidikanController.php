<?php

namespace App\Http\Controllers;

use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GeneralActivityNotification;
use App\Models\User;

class PendidikanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pendidikan::query();

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('pendidikan', 'like', "%{$search}%");
        }

        $pendidikans = $query->orderBy('pendidikan', 'asc')->paginate(10);

        return view('programs.pendidikan.index', compact('pendidikans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pendidikan' => 'required|string|max:50|unique:pendidikans,pendidikan',
        ], [
            'pendidikan.required' => 'Jenjang pendidikan harus diisi.',
            'pendidikan.unique' => 'Jenjang pendidikan sudah ada.',
        ]);

        $pendidikan = Pendidikan::create($validated);

        // Notifikasi
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $pendidikan,
            Auth::user(),
            'Pendidikan',
            'ditambahkan'
        ));

        return redirect()->route('admin.pendidikan.index')
            ->with('success', 'Jenjang pendidikan berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pendidikan $pendidikan)
    {
        $validated = $request->validate([
            'pendidikan' => 'required|string|max:50|unique:pendidikans,pendidikan,' . $pendidikan->id,
        ], [
            'pendidikan.required' => 'Jenjang pendidikan harus diisi.',
            'pendidikan.unique' => 'Jenjang pendidikan sudah ada.',
        ]);

        $pendidikan->update($validated);

        // Notifikasi
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new GeneralActivityNotification(
            $pendidikan,
            Auth::user(),
            'Pendidikan',
            'diperbarui'
        ));

        return redirect()->route('admin.pendidikan.index')
            ->with('success', 'Jenjang pendidikan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pendidikan $pendidikan)
    {
        // Cek apakah sedang digunakan
        $usedByParticipants = $pendidikan->participants()->count();
        $usedByInstructors = $pendidikan->instructors()->count();

        if ($usedByParticipants > 0 || $usedByInstructors > 0) {
            return redirect()->route('admin.pendidikan.index')
                ->with('error', 'Jenjang pendidikan tidak dapat dihapus karena sedang digunakan oleh ' . 
                    ($usedByParticipants + $usedByInstructors) . ' data.');
        }

        $pendidikan->delete();

        return redirect()->route('admin.pendidikan.index')
            ->with('success', 'Jenjang pendidikan berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('pengaturan.index');
    }

    public function update(Request $request)
    {
        return redirect()->route('pengaturan.index')->with('success', 'Pengaturan berhasil disimpan');
    }
}
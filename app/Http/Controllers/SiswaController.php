<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        return view('siswa.index');
    }

    public function create()
    {
        return view('siswa.create');
    }

    public function store(Request $request)
    {
        // Logic untuk menyimpan data siswa
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan');
    }

    public function show($id)
    {
        return view('siswa.show', compact('id'));
    }

    public function edit($id)
    {
        return view('siswa.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Logic untuk update data siswa
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diupdate');
    }

    public function destroy($id)
    {
        // Logic untuk hapus data siswa
        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus');
    }
}
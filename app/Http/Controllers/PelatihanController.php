<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelatihanController extends Controller
{
    public function index()
    {
        return view('pelatihan.kelola.index');
    }

    public function create()
    {
        return view('pelatihan.kelola.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('pelatihan.kelola.index')->with('success', 'Pelatihan berhasil ditambahkan');
    }

    public function show($id)
    {
        return view('pelatihan.kelola.show', compact('id'));
    }

    public function edit($id)
    {
        return view('pelatihan.kelola.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('pelatihan.kelola.index')->with('success', 'Pelatihan berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('pelatihan.kelola.index')->with('success', 'Pelatihan berhasil dihapus');
    }
}
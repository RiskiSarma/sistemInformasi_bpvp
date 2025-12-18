<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProgramPelatihanController extends Controller
{
    public function index()
    {
        return view('pelatihan.program.index');
    }

    public function create()
    {
        return view('pelatihan.program.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('pelatihan.program.index')->with('success', 'Program pelatihan berhasil ditambahkan');
    }

    public function show($id)
    {
        return view('pelatihan.program.show', compact('id'));
    }

    public function edit($id)
    {
        return view('pelatihan.program.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('pelatihan.program.index')->with('success', 'Program pelatihan berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('pelatihan.program.index')->with('success', 'Program pelatihan berhasil dihapus');
    }
}
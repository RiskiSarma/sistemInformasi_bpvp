<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitKompetensiController extends Controller
{
    public function index()
    {
        return view('pelatihan.unit-kompetensi.index');
    }

    public function create()
    {
        return view('pelatihan.unit-kompetensi.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('pelatihan.unit-kompetensi.index')->with('success', 'Unit kompetensi berhasil ditambahkan');
    }

    public function show($id)
    {
        return view('pelatihan.unit-kompetensi.show', compact('id'));
    }

    public function edit($id)
    {
        return view('pelatihan.unit-kompetensi.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('pelatihan.unit-kompetensi.index')->with('success', 'Unit kompetensi berhasil diupdate');
    }

    public function destroy($id)
    {
        return redirect()->route('pelatihan.unit-kompetensi.index')->with('success', 'Unit kompetensi berhasil dihapus');
    }
}
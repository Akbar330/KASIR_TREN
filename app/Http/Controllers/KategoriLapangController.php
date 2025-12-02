<?php

namespace App\Http\Controllers;

use App\Models\KategoriLapang;
use Illuminate\Http\Request;

class KategoriLapangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = KategoriLapang::all();
        return view('kategorilapang.index', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        KategoriLapang::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('kategori-lapangan.index')->with('success', 'Kategori berhasil ditambahkan.');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriLapang $kategoriLapang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $kategoriLapang->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('kategori-lapangan.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriLapang $kategoriLapang)
    {
        $kategoriLapang->delete();

        return redirect()->route('kategori-lapangan.index')->with('success', 'Kategori berhasil dihapus.');
    }
}

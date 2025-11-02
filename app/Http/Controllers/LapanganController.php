<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;

class LapanganController extends Controller
{
    public function index()
    {
        $lapangans = Lapangan::latest()->get();
        return view('lapangan.index', compact('lapangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:vinyl,rumput_sintetis,matras',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,maintenance',
            'keterangan' => 'nullable|string'
        ]);

        Lapangan::create($request->all());

        return redirect()->route('lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan');
    }

    public function update(Request $request, Lapangan $lapangan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:vinyl,rumput_sintetis,matras',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,maintenance',
            'keterangan' => 'nullable|string'
        ]);

        $lapangan->update($request->all());

        return redirect()->route('lapangan.index')
            ->with('success', 'Lapangan berhasil diupdate');
    }

    public function destroy(Lapangan $lapangan)
    {
        $lapangan->delete();
        return redirect()->route('lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus');
    }
}

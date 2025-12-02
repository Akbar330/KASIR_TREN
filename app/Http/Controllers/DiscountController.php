<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->get();
        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_voucher' => 'required|unique:discounts,kode_voucher',
            'type' => 'required',
            'value' => 'required|numeric',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            // 'max_penggunaan' => 'required|integer',
        ]);


        Discount::create($request->all());
        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil dibuat.');
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'kode_voucher' => 'required|unique:discounts,kode_voucher,' . $discount->id,
            'type' => 'required|in:percent,amount',
            'value' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'aktif' => 'required|boolean',
        ]);

        $discount->update($request->all());

        return redirect()->route('discounts.index')->with('success', 'Diskon berhasil diperbarui.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return redirect()->route('discounts.index')->with('success', 'Diskon dihapus.');
    }
}

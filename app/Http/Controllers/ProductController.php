<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:minuman,makanan,equipment',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'barcode' => 'nullable|unique:products,barcode'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:minuman,makanan,equipment',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }

    public function search(Request $request)
    {
        $term = $request->get('term');
        $products = Product::where('nama', 'LIKE', "%{$term}%")
            ->orWhere('barcode', 'LIKE', "%{$term}%")
            ->where('stok', '>', 0)
            ->limit(10)
            ->get();

        return response()->json($products);
    }
}

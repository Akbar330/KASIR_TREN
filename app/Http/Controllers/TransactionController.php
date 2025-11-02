<?php
// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\Lapangan;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['customer', 'lapangan', 'user'])
            ->latest()
            ->paginate(20);
        
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $customers = Customer::all();
        $lapangan = Lapangan::where('status', 'aktif')->get();
        $products = Product::where('stok', '>', 0)->get();
        
        return view('transactions.create', compact('customers', 'lapangan', 'products'));
    }

    public function checkAvailability(Request $request)
    {
        $lapangan = Lapangan::find($request->lapangan_id);
        
        if (!$lapangan) {
            return response()->json(['available' => false, 'message' => 'Lapangan tidak ditemukan']);
        }

        $available = $lapangan->isAvailable(
            $request->tanggal_main,
            $request->jam_mulai,
            $request->jam_selesai
        );

        return response()->json([
            'available' => $available,
            'message' => $available ? 'Lapangan tersedia' : 'Lapangan sudah dibooking di jam tersebut'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal_main' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'bayar' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris'
        ]);

        DB::beginTransaction();
        try {
            $lapangan = Lapangan::findOrFail($request->lapangan_id);
            
            // Cek ketersediaan lapangan
            if (!$lapangan->isAvailable($request->tanggal_main, $request->jam_mulai, $request->jam_selesai)) {
                return back()->with('error', 'Lapangan tidak tersedia di waktu yang dipilih');
            }

            // Hitung durasi
            $jam_mulai = \Carbon\Carbon::parse($request->jam_mulai);
            $jam_selesai = \Carbon\Carbon::parse($request->jam_selesai);
            $durasi_jam = $jam_mulai->diffInHours($jam_selesai);

            // Hitung subtotal lapangan
            $subtotal_lapangan = $durasi_jam * $lapangan->harga_per_jam;

            // Hitung subtotal produk
            $subtotal_produk = 0;
            $products = $request->input('products', []);
            
            foreach ($products as $item) {
                if (isset($item['product_id']) && isset($item['qty']) && $item['qty'] > 0) {
                    $product = Product::find($item['product_id']);
                    if ($product && $product->stok >= $item['qty']) {
                        $subtotal_produk += $product->harga * $item['qty'];
                    }
                }
            }

            $total = $subtotal_lapangan + $subtotal_produk;
            $bayar = $request->bayar;
            $kembalian = $bayar - $total;

            if ($kembalian < 0) {
                return back()->with('error', 'Pembayaran kurang dari total');
            }

            // Simpan transaksi
            $transaction = Transaction::create([
                'kode_transaksi' => Transaction::generateKodeTransaksi(),
                'user_id' => auth()->id(),
                'customer_id' => $request->customer_id,
                'lapangan_id' => $request->lapangan_id,
                'tanggal_main' => $request->tanggal_main,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'durasi_jam' => $durasi_jam,
                'subtotal_lapangan' => $subtotal_lapangan,
                'subtotal_produk' => $subtotal_produk,
                'total' => $total,
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'status_booking' => 'pending',
                'payment_method' => $request->payment_method
            ]);

            // Simpan detail transaksi produk
            foreach ($products as $item) {
                if (isset($item['product_id']) && isset($item['qty']) && $item['qty'] > 0) {
                    $product = Product::find($item['product_id']);
                    
                    if ($product && $product->stok >= $item['qty']) {
                        TransactionDetail::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'qty' => $item['qty'],
                            'harga' => $product->harga,
                            'subtotal' => $product->harga * $item['qty']
                        ]);

                        // Kurangi stok
                        $product->decrement('stok', $item['qty']);
                    }
                }
            }

            DB::commit();

            return redirect()->route('transactions.print', $transaction->id)
                ->with('success', 'Transaksi berhasil dibuat dan booking telah terdaftar');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $transaction = Transaction::with(['customer', 'lapangan', 'user', 'details.product'])
            ->findOrFail($id);
        
        return view('transactions.print', compact('transaction'));
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        $request->validate([
            'status_booking' => 'required|in:pending,selesai,dibatalkan'
        ]);

        $transaction->update([
            'status_booking' => $request->status_booking
        ]);

        // Jika dibatalkan, kembalikan stok produk
        if ($request->status_booking === 'dibatalkan') {
            foreach ($transaction->details as $detail) {
                $detail->product->increment('stok', $detail->qty);
            }
        }

        return back()->with('success', 'Status booking berhasil diupdate');
    }

    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Kembalikan stok produk jika transaksi dihapus
        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok', $detail->qty);
        }

        $transaction->details()->delete();
        $transaction->delete();

        return back()->with('success', 'Transaksi berhasil dihapus');
    }

    public function show($id)
    {
        $transaction = Transaction::with(['customer', 'lapangan', 'user', 'details.product'])
            ->findOrFail($id);
        
        return view('transactions.show', compact('transaction'));
    }
}
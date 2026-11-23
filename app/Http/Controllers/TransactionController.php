<?php
// app/Http/Controllers/TransactionController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Lapangan;
use App\Models\Transaction;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\TransactionDetail;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['customer', 'lapangan', 'user']);

        // Filter tanggal (tanggal transaksi dibuat)
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_booking', $request->status);
        }

        // Filter metode pembayaran
        if ($request->filled('payment')) {
            $query->where('payment_method', $request->payment);
        }

        $transactions = $query->latest()->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $discounts = Discount::where('aktif', true)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->get();
        $customers = Customer::all();
        $lapangan = Lapangan::where('status', operator: 'aktif')->get()->groupBy(fn($lap) => $lap->kategori->nama);;
        // dd($lapangan);
        $products = Product::where('stok', '>', 0)->get();

        return view('transactions.create', compact('customers', 'lapangan', 'products', 'discounts'));
    }

    public function checkAvailability(Request $request)
    {
        $lapanganId = $request->lapangan_id;
        $tanggalMain = $request->tanggal_main;
        $jamMulai = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;

        // Debug: Log data yang diterima
        \Log::info('Check Availability:', [
            'lapangan_id' => $lapanganId,
            'tanggal_main' => $tanggalMain,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai
        ]);

        // Cek apakah ada transaksi yang bentrok
        $conflict = Transaction::where('lapangan_id', $lapanganId)
            ->where('tanggal_main', $tanggalMain)
            ->where('status_booking', '!=', 'dibatalkan')
            ->where(function ($query) use ($jamMulai, $jamSelesai) {
                $query->where(function ($q) use ($jamMulai, $jamSelesai) {
                    // Case 1: Jam mulai booking baru berada di tengah booking lama
                    $q->where('jam_mulai', '<', $jamSelesai)
                        ->where('jam_mulai', '>=', $jamMulai);
                })
                    ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                        // Case 2: Jam selesai booking baru berada di tengah booking lama
                        $q->where('jam_selesai', '>', $jamMulai)
                            ->where('jam_selesai', '<=', $jamSelesai);
                    })
                    ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                        // Case 3: Booking baru melingkupi booking lama
                        $q->where('jam_mulai', '>=', $jamMulai)
                            ->where('jam_selesai', '<=', $jamSelesai);
                    })
                    ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                        // Case 4: Booking lama melingkupi booking baru
                        $q->where('jam_mulai', '<=', $jamMulai)
                            ->where('jam_selesai', '>=', $jamSelesai);
                    });
            })
            ->get();

        // Debug: Log hasil query
        \Log::info('Conflicting transactions:', $conflict->toArray());

        if ($conflict->count() > 0) {
            return response()->json([
                'available' => false,
                'message' => 'Lapangan sudah dibooking pada waktu tersebut. Ada ' . $conflict->count() . ' booking yang bentrok.'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Lapangan tersedia'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal_main' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'bayar' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
            'products' => 'nullable|array',
            'products.*.product_id' => 'nullable|exists:products,id',
            'products.*.qty' => 'nullable|integer|min:1',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        try {
            $jamMulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
            $jamSelesai = Carbon::createFromFormat('H:i', $request->jam_selesai);
            $durasiMenit = $jamMulai->diffInMinutes($jamSelesai);
            if ($durasiMenit % 60 !== 0) {
                return back()->withInput()->withErrors(['jam_selesai' => 'Durasi booking harus dalam kelipatan satu jam.']);
            }

            $lapangan = Lapangan::where('status', 'aktif')->findOrFail($request->lapangan_id);
            $products = collect($request->input('products', []))->filter(fn ($item) => !empty($item['product_id']))
                ->groupBy('product_id')->map(fn ($items) => $items->sum(fn ($item) => (int) ($item['qty'] ?? 0)));

            $transaction = DB::transaction(function () use ($request, $jamMulai, $jamSelesai, $durasiMenit, $lapangan, $products) {
                $lapangan = Lapangan::whereKey($lapangan->id)->lockForUpdate()->firstOrFail();
                if (!$lapangan->isAvailable($request->tanggal_main, $jamMulai->format('H:i:s'), $jamSelesai->format('H:i:s'))) {
                    throw new \RuntimeException('Lapangan tidak tersedia pada waktu yang dipilih. Silakan pilih jadwal lain.');
                }
                $durasiJam = (int) ($durasiMenit / 60);
                $subtotalLapangan = $durasiJam * $lapangan->harga_per_jam;
                $subtotalProduk = 0;
                $lockedProducts = [];
                foreach ($products as $productId => $qty) {
                    if ($qty < 1) throw new \RuntimeException('Jumlah produk harus minimal satu.');
                    $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();
                    if ($product->stok < $qty) throw new \RuntimeException("Stok {$product->nama} tidak mencukupi. Tersedia {$product->stok}.");
                    $subtotalProduk += $product->harga * $qty;
                    $lockedProducts[] = [$product, $qty];
                }

                $subtotal = $subtotalLapangan + $subtotalProduk;
                $discount = null;
                $discountAmount = 0;
                if ($request->filled('discount_id')) {
                    $discount = Discount::whereKey($request->discount_id)->lockForUpdate()->firstOrFail();
                    $valid = $discount->aktif
                        && (!$discount->tanggal_mulai || $discount->tanggal_mulai <= $request->tanggal_main)
                        && (!$discount->tanggal_selesai || $discount->tanggal_selesai >= $request->tanggal_main)
                        && (!$discount->max_penggunaan || $discount->digunakan < $discount->max_penggunaan);
                    if (!$valid) throw new \RuntimeException('Voucher sudah tidak aktif atau kuotanya habis.');
                    $discountAmount = $discount->type === 'percent' ? $subtotal * ($discount->value / 100) : min($subtotal, $discount->value);
                }
                $discountAmount = round($discountAmount, 2);
                $total = max(0, $subtotal - $discountAmount);
                $bayar = (float) $request->bayar;
                if ($bayar < $total) throw new \RuntimeException('Pembayaran kurang dari total tagihan.');

                $trx = Transaction::create([
                    'kode_transaksi' => Transaction::generateKodeTransaksi(),
                    'user_id' => auth()->id(), 'customer_id' => $request->customer_id,
                    'lapangan_id' => $lapangan->id, 'tanggal_main' => $request->tanggal_main,
                    'jam_mulai' => $jamMulai->format('H:i:s'), 'jam_selesai' => $jamSelesai->format('H:i:s'),
                    'durasi_jam' => $durasiJam, 'subtotal_lapangan' => $subtotalLapangan,
                    'subtotal_produk' => $subtotalProduk, 'discount_id' => $discount?->id,
                    'discount_type' => $discount?->type, 'discount_percent' => $subtotal > 0 ? ($discountAmount / $subtotal) * 100 : 0,
                    'discount_amount' => $discountAmount, 'total' => $total, 'bayar' => $bayar,
                    'kembalian' => $bayar - $total, 'status_booking' => 'selesai', 'payment_method' => $request->payment_method,
                ]);
                foreach ($lockedProducts as [$product, $qty]) {
                    TransactionDetail::create(['transaction_id' => $trx->id, 'product_id' => $product->id,
                        'qty' => $qty, 'harga' => $product->harga, 'subtotal' => $product->harga * $qty]);
                    $product->decrement('stok', $qty);
                }
                if ($discount) $discount->increment('digunakan');
                return $trx;
            });

            return redirect()->route('transactions.create')
                ->with('created_transaction_id', $transaction->id);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function print($id)
    {
        $transaction = Transaction::with(['customer', 'lapangan', 'user', 'details.product', 'discount'])
            ->findOrFail($id);

        return view('transactions.print', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = Transaction::with(['customer', 'lapangan', 'details.product'])->findOrFail($id);
        $customers = Customer::all();
        $lapangan = Lapangan::where('status', 'aktif')->get();
        $products = Product::where('stok', '>', 0)->get();

        return view('transactions.edit', compact('transaction', 'customers', 'lapangan', 'products'));
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'tanggal_main' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'payment_method' => 'required|in:cash,transfer,qris',
        ]);

        // Hitung ulang durasi
        // Paksa jam ke format 24 jam (H:i)
        $jam_mulai = \Carbon\Carbon::parse($request->jam_mulai)->format('H:i');
        $jam_selesai = \Carbon\Carbon::parse($request->jam_selesai)->format('H:i');

        // Hitung durasi
        $durasi_jam = \Carbon\Carbon::parse($jam_mulai)->diffInHours(\Carbon\Carbon::parse($jam_selesai));

        $lapangan = Lapangan::findOrFail($request->lapangan_id);
        $subtotal_lapangan = $durasi_jam * $lapangan->harga_per_jam;

        // Update data transaksi
        $transaction->update([
            'customer_id' => $request->customer_id,
            'lapangan_id' => $request->lapangan_id,
            'tanggal_main' => $request->tanggal_main,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'durasi_jam' => $durasi_jam,
            'subtotal_lapangan' => $subtotal_lapangan,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
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
        $user = auth()->user();

        // Kalau role Kasir -> minta approval Admin
        if ($user->isKasir()) {
            $transaction->update([
                'status_booking' => 'pending_cancel',
                'cancel_requested' => true,
                'cancel_requested_by' => $user->id,
            ]);

            return back()->with('info', 'Permintaan pembatalan telah dikirim ke admin untuk disetujui.');
        }

        // Kalau role Admin -> langsung batalkan
        if ($user->isAdmin()) {
            foreach ($transaction->details as $detail) {
                $detail->product->increment('stok', $detail->qty);
            }

            $transaction->update([
                'status_booking' => 'dibatalkan',
                'cancel_requested' => false,
            ]);

            return back()->with('success', 'Transaksi berhasil dibatalkan oleh admin.');
        }

        return back()->with('error', 'Anda tidak memiliki izin untuk menghapus transaksi.');
    }


    public function show($id)
    {
        $transaction = Transaction::with(['customer', 'lapangan', 'user', 'details.product'])
            ->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }

    // Menampilkan daftar permintaan pembatalan
    public function cancelRequests()
    {
        $transactions = Transaction::with(['customer', 'lapangan', 'user'])
            ->where('cancel_requested', true)
            ->where('status_booking', 'pending_cancel')
            ->latest()
            ->get();

        return view('transactions.cancel_requests', compact('transactions'));
    }

    // Admin menyetujui pembatalan
    public function approveCancel($id)
    {
        $transaction = Transaction::findOrFail($id);

        foreach ($transaction->details as $detail) {
            $detail->product->increment('stok', $detail->qty);
        }

        $transaction->update([
            'status_booking' => 'dibatalkan',
            'cancel_requested' => false,
        ]);

        return back()->with('success', 'Permintaan pembatalan disetujui. Transaksi telah dibatalkan.');
    }
}

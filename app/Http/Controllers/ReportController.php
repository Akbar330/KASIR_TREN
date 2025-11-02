<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin');
    }

    public function omset(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'lapangan', 'user'])
            ->latest()
            ->get();

        $totalOmset = $transactions->sum('total');
        $totalTransaksi = $transactions->count();
        $totalLapangan = $transactions->sum('subtotal_lapangan');
        $totalProduk = $transactions->sum('subtotal_produk');

        // Omset per hari
        $omsetPerHari = $transactions->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        })->map(function($items) {
            return [
                'tanggal' => $items->first()->created_at->format('d M Y'),
                'total' => $items->sum('total'),
                'count' => $items->count()
            ];
        });

        // Omset per lapangan
        $omsetPerLapangan = $transactions->groupBy('lapangan_id')->map(function($items) {
            return [
                'lapangan' => $items->first()->lapangan->nama,
                'total' => $items->sum('subtotal_lapangan'),
                'count' => $items->count()
            ];
        });

        return view('reports.omset', compact(
            'transactions', 'totalOmset', 'totalTransaksi', 
            'totalLapangan', 'totalProduk', 'omsetPerHari', 
            'omsetPerLapangan', 'startDate', 'endDate'
        ));
    }

    public function product(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $productSales = TransactionDetail::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('product')
        ->get()
        ->groupBy('product_id')
        ->map(function($items) {
            $product = $items->first()->product;
            return [
                'nama' => $product->nama,
                'kategori' => $product->kategori,
                'qty_terjual' => $items->sum('qty'),
                'total_penjualan' => $items->sum('subtotal'),
                'stok_tersisa' => $product->stok
            ];
        })
        ->sortByDesc('qty_terjual');

        return view('reports.product', compact('productSales', 'startDate', 'endDate'));
    }

    public function exportOmset(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'lapangan', 'user'])
            ->latest()
            ->get();

        $filename = "laporan_omset_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Transaksi', 'Tanggal', 'Customer', 'Lapangan', 'Durasi', 'Subtotal Lapangan', 'Subtotal Produk', 'Total', 'Kasir']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->kode_transaksi,
                    $transaction->created_at->format('d-m-Y H:i'),
                    $transaction->customer->nama,
                    $transaction->lapangan->nama,
                    $transaction->durasi_jam . ' jam',
                    $transaction->subtotal_lapangan,
                    $transaction->subtotal_produk,
                    $transaction->total,
                    $transaction->user->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
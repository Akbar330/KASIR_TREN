<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Lapangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } else {
            return $this->kasirDashboard();
        }
    }

    private function adminDashboard()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Omset hari ini
        $omsetToday = Transaction::whereDate('created_at', $today)->sum('total');
        
        // Omset bulan ini
        $omsetMonth = Transaction::where('created_at', '>=', $thisMonth)->sum('total');

        // Total transaksi hari ini
        $transaksiToday = Transaction::whereDate('created_at', $today)->count();

        // Lapangan paling sering dibooking
        $popularLapangan = Transaction::selectRaw('lapangan_id, COUNT(*) as total')
            ->groupBy('lapangan_id')
            ->with('lapangan')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // Grafik omset 7 hari terakhir
        $omsetChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $omsetChart[] = [
                'date' => $date->format('d M'),
                'total' => Transaction::whereDate('created_at', $date)->sum('total')
            ];
        }

        // Produk stok menipis
        $lowStockProducts = Product::where('stok', '<=', 10)->orderBy('stok')->get();

        // Booking hari ini
        $bookingsToday = Transaction::whereDate('tanggal_main', $today)
            ->where('status_booking', 'pending')
            ->with(['customer', 'lapangan'])
            ->orderBy('jam_mulai')
            ->get();

        return view('dashboard.admin', compact(
            'omsetToday',
            'omsetMonth',
            'transaksiToday',
            'popularLapangan',
            'omsetChart',
            'lowStockProducts',
            'bookingsToday'
        ));
    }

    private function kasirDashboard()
    {
        $today = Carbon::today();

        // Transaksi hari ini oleh kasir yang login
        $myTransactionsToday = Transaction::where('user_id', auth()->id())
            ->whereDate('created_at', $today)
            ->count();

        // Booking hari ini
        $bookingsToday = Transaction::whereDate('tanggal_main', $today)
            ->where('status_booking', 'pending')
            ->with(['customer', 'lapangan'])
            ->orderBy('jam_mulai')
            ->get();

        // Transaksi terakhir oleh kasir
        $recentTransactions = Transaction::where('user_id', auth()->id())
            ->with(['customer', 'lapangan'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.kasir', compact(
            'myTransactionsToday',
            'bookingsToday',
            'recentTransactions'
        ));
    }
}
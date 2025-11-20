<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $lapangan_id = $request->input('lapangan_id');

        $lapangans = Lapangan::where('status', 'aktif')->get();

        $bookings = Transaction::with(['customer', 'lapangan', 'user'])
            ->where('tanggal_main', $tanggal)
            ->whereIn('status_booking', ['pending', 'aktif', 'selesai'])
            ->when($lapangan_id, function ($query) use ($lapangan_id) {
                return $query->where('lapangan_id', $lapangan_id);
            })
            ->orderBy('jam_mulai')
            ->get();

        // Generate jadwal per jam (06:00 - 24:00)
        $jadwal = [];
        for ($i = 6; $i < 24; $i++) {
            $jam = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $jadwal[] = $jam;
        }

        return view('bookings.index', compact('bookings', 'lapangans', 'tanggal', 'jadwal', 'lapangan_id'));
    }

    public function calendar()
    {
        $bookings = Transaction::with(['customer', 'lapangan'])
            ->where('status_booking', 'pending')
            ->get()
            ->map(function ($booking) {
                return [
                    'title' => $booking->customer->nama . ' - ' . $booking->lapangan->nama,
                    'start' => $booking->tanggal_main . ' ' . $booking->jam_mulai,
                    'end' => $booking->tanggal_main . ' ' . $booking->jam_selesai,
                    'backgroundColor' => $this->getColorByLapangan($booking->lapangan_id),
                    'borderColor' => $this->getColorByLapangan($booking->lapangan_id),
                ];
            });

        return view('bookings.calendar', compact('bookings'));
    }

    private function getColorByLapangan($lapangan_id)
    {
        $colors = ['#3788d8', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'];
        return $colors[($lapangan_id - 1) % count($colors)];
    }
}

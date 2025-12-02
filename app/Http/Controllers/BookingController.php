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

        // Jika user memilih lapangan → filter lapangan juga
        $lapangans = Lapangan::when($lapangan_id, function ($q) use ($lapangan_id) {
            return $q->where('id', $lapangan_id);
        })
            ->where('status', 'aktif')
            ->get();

        // Filter bookings
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

        return view('bookings.index', compact(
            'bookings',
            'lapangans',
            'tanggal',
            'jadwal',
            'lapangan_id'
        ));
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

    /**
     * Check availability of lapangan
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal_main' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $lapangan_id = $request->lapangan_id;
        $tanggal_main = $request->tanggal_main;
        $jam_mulai = $request->jam_mulai;
        $jam_selesai = $request->jam_selesai;
        $transaction_id = $request->transaction_id; // Untuk edit booking

        // Cek apakah ada transaksi yang bentrok
        $existingBooking = Transaction::where('lapangan_id', $lapangan_id)
            ->where('tanggal_main', $tanggal_main)
            ->whereIn('status_booking', ['pending', 'aktif']) // Hanya cek yang masih aktif
            ->when($transaction_id, function ($query) use ($transaction_id) {
                // Abaikan transaksi yang sedang diedit
                return $query->where('id', '!=', $transaction_id);
            })
            ->where(function($query) use ($jam_mulai, $jam_selesai) {
                // Cek overlap waktu dengan berbagai kondisi
                $query->where(function($q) use ($jam_mulai, $jam_selesai) {
                    // Kondisi 1: Jam mulai baru berada di antara booking yang ada
                    $q->where('jam_mulai', '<=', $jam_mulai)
                      ->where('jam_selesai', '>', $jam_mulai);
                })
                ->orWhere(function($q) use ($jam_mulai, $jam_selesai) {
                    // Kondisi 2: Jam selesai baru berada di antara booking yang ada
                    $q->where('jam_mulai', '<', $jam_selesai)
                      ->where('jam_selesai', '>=', $jam_selesai);
                })
                ->orWhere(function($q) use ($jam_mulai, $jam_selesai) {
                    // Kondisi 3: Booking baru mencakup seluruh booking yang ada
                    $q->where('jam_mulai', '>=', $jam_mulai)
                      ->where('jam_selesai', '<=', $jam_selesai);
                });
            })
            ->with(['customer', 'lapangan'])
            ->first();

        if ($existingBooking) {
            $lapangan = Lapangan::find($lapangan_id);
            
            return response()->json([
                'available' => false,
                'message' => sprintf(
                    'Lapangan %s sudah dibooking pada tanggal %s pukul %s - %s oleh %s',
                    $lapangan->nama ?? 'ini',
                    Carbon::parse($tanggal_main)->format('d/m/Y'),
                    Carbon::parse($existingBooking->jam_mulai)->format('H:i'),
                    Carbon::parse($existingBooking->jam_selesai)->format('H:i'),
                    $existingBooking->customer->nama ?? 'customer lain'
                ),
                'booking' => [
                    'customer' => $existingBooking->customer->nama ?? 'N/A',
                    'jam_mulai' => Carbon::parse($existingBooking->jam_mulai)->format('H:i'),
                    'jam_selesai' => Carbon::parse($existingBooking->jam_selesai)->format('H:i'),
                ]
            ]);
        }

        // Validasi tambahan: cek apakah tanggal tidak di masa lalu
        $today = Carbon::today()->format('Y-m-d');
        if ($tanggal_main < $today) {
            return response()->json([
                'available' => false,
                'message' => 'Tidak dapat membuat booking untuk tanggal yang sudah lewat'
            ]);
        }

        // Validasi: cek jam operasional (opsional)
        $jamMulaiInt = (int) substr($jam_mulai, 0, 2);
        $jamSelesaiInt = (int) substr($jam_selesai, 0, 2);
        
        if ($jamMulaiInt < 6 || $jamSelesaiInt > 24) {
            return response()->json([
                'available' => false,
                'message' => 'Jam operasional lapangan: 06:00 - 24:00'
            ]);
        }

        $lapangan = Lapangan::find($lapangan_id);
        
        return response()->json([
            'available' => true,
            'message' => sprintf(
                'Lapangan %s tersedia untuk tanggal %s pukul %s - %s',
                $lapangan->nama ?? 'ini',
                Carbon::parse($tanggal_main)->format('d/m/Y'),
                Carbon::parse($jam_mulai)->format('H:i'),
                Carbon::parse($jam_selesai)->format('H:i')
            )
        ]);
    }

    /**
     * Get bookings for specific date and lapangan (untuk AJAX)
     */
    public function getBookings(Request $request)
    {
        $lapangan_id = $request->lapangan_id;
        $tanggal = $request->tanggal ?? date('Y-m-d');

        $bookings = Transaction::with(['customer'])
            ->where('lapangan_id', $lapangan_id)
            ->where('tanggal_main', $tanggal)
            ->whereIn('status_booking', ['pending', 'aktif'])
            ->orderBy('jam_mulai')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'customer' => $booking->customer->nama ?? 'N/A',
                    'jam_mulai' => Carbon::parse($booking->jam_mulai)->format('H:i'),
                    'jam_selesai' => Carbon::parse($booking->jam_selesai)->format('H:i'),
                    'status' => $booking->status_booking,
                ];
            });

        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    private function getColorByLapangan($lapangan_id)
    {
        $colors = ['#3788d8', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6'];
        return $colors[($lapangan_id - 1) % count($colors)];
    }
}
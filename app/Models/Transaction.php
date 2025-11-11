<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'customer_id',
        'lapangan_id',
        'tanggal_main',
        'jam_mulai',
        'jam_selesai',
        'durasi_jam',
        'subtotal_lapangan',
        'subtotal_produk',
        'discount_type',
        'discount_percent',
        'discount_amount',
        'total',
        'bayar',
        'kembalian',
        'status_booking',
        'payment_method',
        'cancel_requested',        // 👈 tambah ini
        'cancel_requested_by',     // 👈 dan ini
    ];


    protected $casts = [
        'tanggal_main' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function generateKodeTransaksi()
    {
        $prefix = 'TRX';
        $date = date('Ymd');
        $lastTransaction = self::whereDate('created_at', today())->latest()->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->kode_transaksi, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }
}

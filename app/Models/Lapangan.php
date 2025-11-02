<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;
    
    protected $table = 'lapangan';
    
    protected $fillable = [
        'nama', 'jenis', 'harga_per_jam', 'status', 'keterangan'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isAvailable($tanggal, $jam_mulai, $jam_selesai)
    {
        return !$this->transactions()
            ->where('tanggal_main', $tanggal)
            ->where('status_booking', 'pending')
            ->where(function($query) use ($jam_mulai, $jam_selesai) {
                $query->whereBetween('jam_mulai', [$jam_mulai, $jam_selesai])
                      ->orWhereBetween('jam_selesai', [$jam_mulai, $jam_selesai])
                      ->orWhere(function($q) use ($jam_mulai, $jam_selesai) {
                          $q->where('jam_mulai', '<=', $jam_mulai)
                            ->where('jam_selesai', '>=', $jam_selesai);
                      });
            })
            ->exists();
    }
}

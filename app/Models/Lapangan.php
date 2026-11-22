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

    public function isAvailable($tanggal, $jam_mulai, $jam_selesai, $exceptId = null)
{
    $mulai   = \Carbon\Carbon::parse($jam_mulai)->format('H:i:s');
    $selesai = \Carbon\Carbon::parse($jam_selesai)->format('H:i:s');

    return !$this->transactions()
        ->whereDate('tanggal_main', $tanggal)
        ->whereIn('status_booking', Transaction::STATUS_KUNCI)
        ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
        ->where('jam_mulai', '<', $selesai)
        ->where('jam_selesai', '>', $mulai)
        ->exists();
}
}

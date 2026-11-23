<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    protected $table = 'lapangan';

    protected $fillable = [
        'nama',
        'kategori_lapangs_id',
        'harga_per_jam',
        'status',
        'keterangan'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function isAvailable($tanggal, $jam_mulai, $jam_selesai)
    {
        return !$this->transactions()
            ->where('tanggal_main', $tanggal)
            ->where('status_booking', '!=', 'dibatalkan')
            ->where(function ($query) use ($jam_mulai, $jam_selesai) {
                $query->where('jam_mulai', '<', $jam_selesai)
                    ->where('jam_selesai', '>', $jam_mulai);
            })
            ->exists();
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriLapang::class, 'kategori_lapangs_id', 'id');
    }
}

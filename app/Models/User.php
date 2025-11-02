<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


    public function isAdmin()
    {
        return $this->role->name === 'Admin';
    }

    public function isKasir()
    {
        return $this->role->name === 'Kasir';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // kolom yang boleh di‐mass assign
    protected $fillable = ['username', 'password'];

    // sembunyikan password
    protected $hidden = ['password', 'remember_token'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'users_id');
    }
}

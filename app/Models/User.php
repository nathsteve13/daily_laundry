<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['username', 'password','role'];

    protected $hidden = ['password', 'remember_token'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'users_id');
    }

    public function deliveries()
    {
        return $this->hasMany(DeliveryList::class, 'kurir_id');
    }

    public function pickups()
    {
        return $this->hasMany(PickupList::class, 'kurir_id');
    }

}

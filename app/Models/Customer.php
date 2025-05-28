<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone_number',
    ];

    public function transactions()
    {
        return $this->belongsToMany(
            Transaction::class,
            'transactions_has_customers',
            'customers_id',
            'no_transaction'
        );
    }
}

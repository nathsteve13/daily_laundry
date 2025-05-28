<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'no_transaction',
        'pickup',
        'value_per_unit',
        'service_type_id',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'no_transaction', 'no_transaction');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}

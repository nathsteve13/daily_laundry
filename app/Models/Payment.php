<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'no_payment';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_payment',
        'no_transaction',
        'total',
        'status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'no_transaction', 'no_transaction');
    }
}

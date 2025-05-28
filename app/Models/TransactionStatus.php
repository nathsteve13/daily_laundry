<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    protected $table = 'transaction_status';

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'status',
        'no_transaction',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'no_transaction', 'no_transaction');
    }
}

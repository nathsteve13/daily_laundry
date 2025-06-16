<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryList extends Model
{
    protected $table = 'delivery_lists';
    protected $primaryKey = ['no_delivery', 'no_transaction'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'no_delivery',
        'no_transaction',
        'kurir_id',
        'tanggal_diantar',
        'tanggal_terkirim',
        'bukti_terima',
        'created_at',
        'updated_at',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'no_transaction', 'no_transaction');
    }

    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }
}

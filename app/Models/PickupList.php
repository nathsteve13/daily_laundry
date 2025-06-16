<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickupList extends Model
{
    protected $table = 'pickup_lists';
    protected $primaryKey = 'no_pickup';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'no_pickup',
        'no_transaction',
        'kurir_id',
        'tanggal_pengambilan',
        'tanggal_diambil',
        'bukti_pengambilan',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'no_transaction';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_transaction',
        'subtotal',
        'discount',
        'total',
        'users_id',
        'kecamatan_id',
        'kelurahan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'no_transaction', 'no_transaction');
    }

    public function customers()
    {
        return $this->belongsToMany(
            Customer::class,
            'transactions_has_customers',
            'no_transaction',
            'customers_id'
        );
    }

    public function status()
    {
        return $this->hasOne(
            TransactionStatus::class,
            'no_transaction',
            'no_transaction'
        );
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'no_transaction', 'no_transaction');
    }
    public function deliveryLists()
    {
        return $this->hasMany(DeliveryList::class, 'no_transaction', 'no_transaction');
    }

    public function pickupLists()
    {
        return $this->hasMany(PickupList::class, 'no_transaction', 'no_transaction');
    }

    public function transactionStatus()
    {
        return $this->hasMany(TransactionStatus::class, 'no_transaction', 'no_transaction');
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id', 'id');
    }

    /**
     * Get the first order request from the first customer
     */
    public function getOrderRequest()
    {
        return $this->customers->first()?->orderRequests->first();
    }

    /**
     * Get kecamatan name from order request
     */
    public function getKecamatanName()
    {
        return $this->kecamatan->name ?? '-';
    }

    /**
     * Get kelurahan name from order request
     */
    public function getKelurahanName()
    {
        return $this->kelurahan->name ?? '-';
    }
}

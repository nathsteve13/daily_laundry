<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $table = 'service_type';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'unit',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'service_type_id');
    }
}

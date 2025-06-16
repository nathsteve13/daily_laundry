<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    protected $table = 'order_requests';
    protected $primaryKey = 'no_order';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'no_order',
        'name',
        'status',
        'address',
        'phone_number',
        'estimated_value',
        'service_type_id',
        'created_at',
        'updated_at',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}

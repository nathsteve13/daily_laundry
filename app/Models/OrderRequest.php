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
        'delivery_type',
        'created_at',
        'updated_at',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function details()
    {
        return $this->hasMany(OrderRequestDetail::class, 'order_request_no_order', 'no_order');
    }

    public function getDetailsAttribute()
    {
        return $this->details()->with('serviceType')->get()->map(function ($d) {
            return [
                'service_type_id' => $d->service_type_id,
                'service_type_name' => $d->serviceType->name ?? '-',
                'estimated_value' => $d->estimated_value,
                'price' => $d->serviceType->price ?? 0,
            ];
        });
    }
}

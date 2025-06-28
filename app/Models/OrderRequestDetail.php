<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRequestDetail extends Model
{
    protected $table = 'order_request_details';

    protected $fillable = [
        'order_request_no_order',
        'service_type_id',
        'estimated_value',
    ];

    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class, 'order_request_no_order', 'no_order');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}

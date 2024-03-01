<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'phone_number',
        'street_address',
        'district_address',
        'city',
        'order_date',
        'order_status',
        'order_total',
        'shipping_date',
        'tracking_number',
        'carrier',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, "order_id");
    }

    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class, "order_id");
    }
    protected $date = ["deleted_at"];
}

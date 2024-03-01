<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'payment_status',
        'session_id',
        'payment_intent_id',
        'payment_date',
        'payment_due_date',
        'user_id',
        'order_id',
    ];
    protected $date = ["deleted_at"];
}

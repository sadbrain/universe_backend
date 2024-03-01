<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShoppingCart extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'color',
        'size',
        'quantity',
        'product_id',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function product()
    {
        return $this->belongsTo(Product::class, "product_id");
    }
    protected $date = ["deleted_at"];
}

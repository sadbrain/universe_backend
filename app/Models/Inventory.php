<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'quantity',
        'quantity_sold',
    ];
    public function sizes()
    {
        return $this->hasMany(ProductSize::class, "inventory_id");
    }

    public function colors(){
        return $this->hasMany(ProductColor::class, "inventory_id");
    }
    protected $date = ["deleted_at"];
}

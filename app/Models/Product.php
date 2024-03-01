<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'description',
        'price',
        'rating',
        'favorites',
        'category_id',
        'inventory_id',
        'discount_id',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, "inventory_id");
    }

    public function product_images(){
        return $this->hasMany(ProductImage::class, "product_id");
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product){
            $product->slug = Str::slug($product->name);
        });
    }
    protected $date = ["deleted_at"];
}

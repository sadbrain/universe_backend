<?php
namespace App\Repository;

use App\Repository\IRepository\IOrderDetailRepository;
use App\Repository\Repository;
use Illuminate\Support\Facades\DB;

class OrderDetailRepository extends Repository implements IOrderDetailRepository {
    public function get_model(){
        return \App\Models\OrderDetail::class;
    }

    public function get_best_seller_products(){
        $top_selling_products = $this->_model::select('product_id', DB::raw('MAX(quantity) as max_quantity'))
        ->groupBy('product_id')
        ->orderByDesc('max_quantity')
        ->limit(12)
        ->get()
        ->pluck('product_id')
        ->toArray();
        $products = (new \App\Models\Product())::query();
        $products = $products->whereIn("id", $top_selling_products)->get()->all();
        return $products;   
    }
}

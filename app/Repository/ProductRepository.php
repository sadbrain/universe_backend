<?php
namespace App\Repository;

use App\Repository\IRepository\IProductRepository;
use App\Repository\Repository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductRepository extends Repository implements IProductRepository {
    public function get_model(){
        return \App\Models\Product::class;
    }

    public function get_best_rating_products(){
        $first_day_of_month = Carbon::now()->startOfMonth();
        $last_day_of_month = Carbon::now()->endOfMonth();
        $top_rating_products = $this->_model::select('*')
        ->whereBetween('created_at', [$first_day_of_month, $last_day_of_month])
        ->orderByDesc('rating')
        ->limit(12)
        ->get()
        ->all();
        return $top_rating_products;   
    }
    
}

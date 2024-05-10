<?php
namespace App\Repository;

use App\Repository\IRepository\IProductSizeRepository;
use App\Repository\Repository;

class ProductSizeRepository extends Repository implements IProductSizeRepository {
    public function get_model(){
        return \App\Models\ProductSize::class;
    }
}

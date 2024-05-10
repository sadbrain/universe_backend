<?php
namespace App\Repository;

use App\Repository\IRepository\IProductColorRepository;
use App\Repository\Repository;

class ProductColorRepository extends Repository implements IProductColorRepository {
    public function get_model(){
        return \App\Models\ProductColor::class;
    }
}

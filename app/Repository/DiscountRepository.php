<?php
namespace App\Repository;

use App\Repository\IRepository\IDiscountRepository;
use App\Repository\Repository;

class DiscountRepository extends Repository implements IDiscountRepository {
    public function get_model(){
        return \App\Models\Discount::class;
    }
}

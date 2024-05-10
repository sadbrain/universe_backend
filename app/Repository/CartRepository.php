<?php
namespace App\Repository;

use App\Repository\IRepository\ICartRepository;
use App\Repository\Repository;

class CartRepository extends Repository implements ICartRepository {
    public function get_model(){
        return \App\Models\ShoppingCart::class;
    }
}

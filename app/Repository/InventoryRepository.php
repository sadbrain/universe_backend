<?php
namespace App\Repository;

use App\Repository\IRepository\IInventoryRepository;
use App\Repository\Repository;

class InventoryRepository extends Repository implements IInventoryRepository {
    public function get_model(){
        return \App\Models\Inventory::class;
    }
}

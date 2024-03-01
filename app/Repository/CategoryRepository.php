<?php
namespace App\Repository;

use App\Repository\IRepository\ICategoryRepository;
use App\Repository\Repository;

class CategoryRepository extends Repository implements ICategoryRepository {
    public function get_model(){
        return \App\Models\Category::class;
    }
}

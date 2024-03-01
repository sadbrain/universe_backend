<?php
namespace App\Repository;

use App\Repository\IRepository\IUnitOfWork;
use App\Repository\IRepository\ICategoryRepository;
use App\Repository\CategoryRepository;

class UnitOfWork implements IUnitOfWork{
    private ICategoryRepository $category;
    public function category(): ICategoryRepository
    {
        return $this->category;
    }

    public function __construct(){
        $this -> category = new CategoryRepository();
    }
}

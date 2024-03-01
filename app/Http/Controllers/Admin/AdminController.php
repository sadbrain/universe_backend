<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repository\IRepository\IUnitOfWork;
use App\Repository\UnitOfWork;

class AdminController extends Controller
{
    protected IUnitOfWork $_unitOfWork;
    public function __construct(UnitOfWork $unitOfWork) {
        $this -> _unitOfWork = $unitOfWork;
    }
    
}

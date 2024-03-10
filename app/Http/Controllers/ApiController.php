<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Controller;
use App\Repository\IRepository\IUnitOfWork;
use App\Repository\UnitOfWork;

class ApiController extends Controller
{
    protected IUnitOfWork $_unitOfWork;
    public function __construct(UnitOfWork $unitOfWork) {
        $this -> _unitOfWork = $unitOfWork;
    }

}

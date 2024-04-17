<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Controller;
use App\Repository\IRepository\IUnitOfWork;
use App\Repository\UnitOfWork;
use JWTAuth;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected IUnitOfWork $_unitOfWork;
    public function __construct(UnitOfWork $unitOfWork) {
        $this -> _unitOfWork = $unitOfWork;
    }

    protected function isAdmin($user){
        $roles = config("constants.role");
        if($user->role->name == $roles["user_admin"] || $user->role->name == $roles["user_employee"])
            return true;
        return false;
    }

    protected function isCompany($user){
        $roles = config("constants.role");
        if($user->role->name == $roles["user_comp"] && $user->company_id != null)
            return true;
        return false;
    }
    protected function getUser(Request $request){
        $token = $request->header('Authorization');
        $user = JWTAuth::parseToken()->authenticate($token);
        return $user;
    }
}

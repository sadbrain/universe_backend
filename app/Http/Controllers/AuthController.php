<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{  
    public function logout() {
        auth()->logout();
        return response()->json(['success_messages' => 'User successfully signed out']);
    }
}

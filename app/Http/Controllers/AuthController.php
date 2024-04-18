<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{  
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|string|email|min:6|max:100',
            'password' => 'required|string|min:6|max:255|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s).*$/',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = JWTAuth::attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function refresh() {
        return $this->createNewToken(auth("api")->refresh());
    }

    protected function createNewToken($token){
        auth()->user()->company;
        auth()->user()->role;
        
        return response()->json([   
            'access_token' => (string) $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
            
        ]);
    }
}

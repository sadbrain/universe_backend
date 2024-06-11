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

        $user = auth()->user();
        // Check if the user is locked
        if ($user->locked) {
            // If user is locked, logout and return error message
            auth()->logout();
            return response()->json(['error' => 'Your account has been locked.'], 401);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|min:6|max:100|unique:users',
            'password' => 'required|string|min:6|max:255|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s).*$/|confirmed',
            'phone' => 'nullable|string|min:10|max:20',
            'street_address' => 'nullable|string|min:6|max:255',
            'district_address' => 'nullable|string|min:6|max:255',
            'city' => 'nullable|string|min:6|max:255',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $roles = config("constants.role");
        $customer_role_db = $this->_unitOfWork->role()->get("name = "."'".$roles["user_cust"]."'");
        $user =  array_merge(
                    $validator->validated(),
                    ['password' => Hash::make($request->password), "role_id" => $customer_role_db->id]
                );
        $this->_unitOfWork->user()->add($user); 
                
        return response()-> json([
            'success_messages' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout() {
        auth()->logout();
        return response()->json(['success_messages' => 'User successfully signed out']);
    }


    public function refresh() {
        return $this->createNewToken(auth("api")->refresh());
    }

    public function userProfile() {
        auth()->user()->company;
        auth()->user()->role;
        return response()->json(auth()->user(), 200);
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

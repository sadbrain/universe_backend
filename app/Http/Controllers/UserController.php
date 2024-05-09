<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends ApiController
{

    public function register(Request $request)
    {
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|min:6|max:100|unique:users',
            'password' => 'required|string|min:6|max:255|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*\W)(?!.*\s).*$/|confirmed',
            'phone' => 'nullable|string|min:10|max:20',
            'street_address' => 'nullable|string|min:6|max:255',
            'district_address' => 'nullable|string|min:6|max:255',
            'city' => 'nullable|string|min:6|max:255',
            "role_id" => 'nullable|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $roles = config("constants.role");
        $customer_role_db = $this->_unitOfWork->role()->get("name = " . "'" . $roles["user_cust"] . "'");
        $company_role_db = $this->_unitOfWork->role()->get("name = " . "'" . $roles["user_comp"] . "'");
        $user = array_merge(
            $validator->validated(),
            ['password' => Hash::make($request->password)]
        );
        if ($user["role_id"] == null) {
            $user["role_id"] = $customer_role_db->id;
        }
        if ($user["role_id"] == $company_role_db->id) $user["company_id"] = $request->company_id;
        $this->_unitOfWork->user()->add($user);
        return response()->json([
            'success_messages' => 'User successfully registered',
            'user' => $user
        ], 201);
    }
    public function getAll(Request $request)
    {
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => ''
        ];
        $users = $this->_unitOfWork->user()->get_all()->with(["company", "role"])->get()->all();
        $response["data"] = $users;
        return response()->json($response, 200);
    }
    public function get(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => ''
        ];
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page";
            return response()->json($response, 403);
        }
        if ($id == null || $id <= 0) {
            $response["error_messages"] = 'Invalid id';
            return response()->json($response, 400);
        }
        $user = $this->_unitOfWork->user()->get("id = $id");
        $user->role;
        if ($user->company_id !== null) $user->company;
        if ($user == null) {
            $response["error_messages"] = 'User not found';
            return response()->json($response, 404);
        }
        $response["data"] = $user;
        return response()->json($response, 200);
    }
    // Ham nay de cap nhat role_id va company_id 
    public function perrmission(Request $request, int $id)
    {
        //luu $response
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => ''
        ];
        // kiem tra account
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = 'You do not have permission to access this page';
            return response()->json($response, 403);
        }
        //validata $role_id va $company_id
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric|min:1',
            "company_id" => 'nullable|numeric|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // kiem tra role_id co bi null hoac <= 0 khoong
        if ($id == null || $id <= 0) {
            $response["error_messages"] = "Invalid id";
            return response()->json($response, 400);
        }
        // kiem tra co ton tai user khong 
        $userDb = $this->_unitOfWork->user()->get("id = $id");
        if ($userDb == null) {
            $response["error_messages"] = "User is not found";
            return response()->json($response, 404);
        }
        // di kiem tra khonng ton tai comany thi mac dinh bang null
        $userDb->fill($validator->validated());
        if (!$this->isCompany($userDb)) {
            $userDb->company_id = null;
        }
        // cho $request->all(), sau do update
        // cuoi cung laf return ve response va status 200
        try {
            $this->_unitOfWork->user()->update($userDb);
            $response["success_messages"] = 'User updated';
            return response()->json($userDb, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function getRoles(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => ''
        ];
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page";
            return response()->json($response, 403);
        }
        $roles = $this->_unitOfWork->role()->get_all()->get()->all();
        $response["data"] = $roles;
        return  response()->json($response, 200);
    }
    public function lockUnlock(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => ''
        ];
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = 'You do not have permission to access this page';
            return response()->json($response, 403);
        }
        if ($id == null || $id <= 0) {
            $response["error_messages"] = 'Invalid id';
            return response()->json($response, 400);
        }
        $userDb = $this->_unitOfWork->user()->get("id = $id");
        if ($userDb == null) {
            $response["error_messages"] = 'User not found';
            return response()->json($response, 404);
        }
        if ($userDb->locked) {
            $userDb->locked = 0;
        } else {
            $userDb->locked = 1;
        }
        $userDb->save();
        $response["success_messages"] = "Operation Successful";
        return response()->json($response, 200);
    }

}

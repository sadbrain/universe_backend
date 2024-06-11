<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Company;
use Illuminate\Http\Request;
use Exception;
use Validator;

class CompanyController extends ApiController
{

    public function getAll(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $query = Company::query();
        $companies = $query->get()->all();
        $response["data"] = $companies;
        return response()->json($response, 200);

        }

    public function get(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $user = $this->getUser($request);

        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        if ($id == null || $id <= 0) {
            $response["error_messages"] = 'Invalid id';
            return response()->json($response, 400);
        }
        $query = Company::query();
        $company = $query->whereRaw("id = $id")->first();

        if ($company == null) {
            $response["error_messages"] = 'Company not found';
            return response()->json($response, 404);
        }

        $response["data"] = $company;
        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:6|max:255',
            'phone_number' => 'required|string|min:10|max:20',
            'street_address' => 'required|string|min:6|max:255',
            'district_address' => 'required|string|min:6|max:255',
            'city' => 'required|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            Company::create($validator->validated());
            $response["success_messages"] = 'Company is created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:6|max:255',
            'phone_number' => 'required|string|min:10|max:20',
            'street_address' => 'required|string|min:6|max:255',
            'district_address' => 'required|string|min:6|max:255',
            'city' => 'required|string|min:6|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($id != $request->input("id")) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        $query = Company::query();
        $company = $query->whereRaw("id = $id")->first();
        
        if ($company == null) {
            $response["error_messages"] = 'Company not found';
            return response()->json($response, 404);
        }

        try {
            $company->update($validator->validated());
            $response["success_messages"] = 'Company is updated successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        $query = Company::query();
        $company = $query->whereRaw("id = $id")->first();

        if ($company == null) {
            $response["error_messages"] = 'Company not found';
            return response()->json($response, 404);
        }

        try {
            // $company->users()->update(['company_id' => null]);
            // $company->delete();
            $response["success_messages"] = 'Company deleted';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

}

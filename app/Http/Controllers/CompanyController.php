<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Exception;
use App\Models\Company;

class CompanyController extends Controller
{
    public function getAll(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        try {
            $companies = Company::all();
            $response['data'] = $companies;
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response['error_messages'] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function get(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        try {
            $company = Company::find($id);
            if (!$company) {
                $response['error_messages'] = 'Company not found';
                return response()->json($response, 404);
            }

            $response['data'] = $company;
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response['error_messages'] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function create(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

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
            $company = Company::create($request->all());
            $response['success_messages'] = 'Company created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response['error_messages'] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

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
            $company = Company::find($id);
            if (!$company) {
                $response['error_messages'] = 'Company not found';
                return response()->json($response, 404);
            }

            $company->update($request->all());
            $response['success_messages'] = 'Company updated successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response['error_messages'] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, ?int $id = null)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        if ($id == null || $id == 0) {
            $response['error_messages'] = 'Bad request id';
            return response()->json($response, 400);
        }

        try {
            $company = Company::find($id);
            if (!$company) {
                $response['error_messages'] = 'Company not found';
                return response()->json($response, 404);
            }

            $company->users()->update(['company_id' => null]);
            $company->delete();

            $response['success_messages'] = 'Company deleted successfully';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response['error_messages'] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
}

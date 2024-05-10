<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Validator;

class CategoryController extends ApiController
{
    public function getAll(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $query = Category::query();
        $categories = $query->get()->all();
        $response["data"] = $categories;
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
        $query = Category::query();
        $category = $query->whereRaw("id = $id")->first();

        if ($category == null) {
            $response["error_messages"] = 'Category not found';
            return response()->json($response, 404);
        }
        $response["data"] = $category;
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
            'name' => 'required|string|min:2|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            Category::create($validator->validated());
            $response["success_messages"] = 'Category is created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
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
        $user = $this->getUser($request);
        if (!$this->isAdmin($user)) {
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
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
        $query = Category::query();
        $category = $query->whereRaw("id = $id")->first();

        if ($category == null) {
            $response["error_messages"] = 'Category is not found';
            return response()->json($response, 404);
        }
        try {
            $category->update($validator->validated());
            $response["success_messages"] = 'Category is updated successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, ?int $id)
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
        $query = Category::query();
        $category = $query->whereRaw("id = $id")->first();
        
        if ($category == null) {
            $response["error_messages"] = 'Category not found';
            return response()->json($response, 404);
        }
        try {
            $category->delete();
            $response["success_messages"] = 'Category deleted';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
}

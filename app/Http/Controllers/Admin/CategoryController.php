<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends AdminController
{  
   
    public function getAll(){
        $categories = $this->_unitOfWork->category()->get_all();
        return response()->json(["data" => $categories]);

    }

    public function get(?int $id){
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
    
        $category = $this->_unitOfWork->category()->get("id = $id");
    
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    
        return response()->json(["data" => $category]);

    }

    public function create(Request $request){
        $data = $request->all();
        try {
            $this->_unitOfWork->category()->add($data);
            return response()->json(['success' => true, 'message' => 'Category created']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
    
    public function update(Request $request,int $id){
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
    
        $category = $this->_unitOfWork->category()->get("id = $id");
    
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    
        $data = $request->all();
        try {
            $this->_unitOfWork->category()->update($data);
            return response()->json(['success' => true, 'message' => 'Category updated']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function delete(?int $id){
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
    
        $category = $this->_unitOfWork->category()->get("id = $id");
    
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    
        try {
            $this->_unitOfWork->category()->delete($category);
            return response()->json(['success' => true, 'message' => 'Category deleted']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
}

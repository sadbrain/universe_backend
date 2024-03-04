<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends AdminController
{  
   
    public function getAll(){
        // di tu Model can lam viec goi ra query de co the su dung
        // cac cau lenh query trong database tren model 

        //query co bang thi no da co du lieu trong database roi
        //vi du tron database minh co 3 category
        //thi query nay no da co 3 category roi nhung no khac voi array
        //no co them nhung ham de query them trong database nhu where select
        $query = Category::query();
        //muon lay tat ca thi ta phai dung ham get(Execute the query as a "select" statement.)
        //all() no se Get all of the items in the collection roi chuyen sang array
        $categories = $query->get()->all();
        // $categories = $this->_unitOfWork->category()->get_all();
        return response()->json(["data" => $categories]);

    }

    public function get(?int $id)
    {
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
    
        //id da hop le roi tiep den tim category theo id
         // di tu Model can lam viec goi ra query de co the su dung
        // cac cau lenh query trong database tren model 

        //query co bang thi no da co du lieu trong database roi
        //vi du tron database minh co 3 category
        //thi query nay no da co 3 category roi nhung no khac voi array
        //no co them nhung ham de query them trong database nhu where select
        // $category = $this->_unitOfWork->category()->get("id = $id");
        $query = Category::query();
        //tu query goi la ham whereRam va truyen dieu kien nhu ben duoi
        //de tim kiem category theo id
        //roi goi thang first de lay thang dau tien
        $category = $query->whereRaw("id = $id")->first();
        //validate category co ton tai hay khong
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    
        return response()->json(["data" => $category]);

    }

    public function create(Request $request){
        //lay du lieu muon tao tu request nhu ben duoi
        $data = $request->all();

        try {

            // $this->_unitOfWork->category()->add($data);
            //tao category
            Category::create($data);
            return response()->json(['success' => true, 'message' => 'Category created']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
    
    public function update(Request $request,int $id){
        //lay du lieu muon tao tu request nhu ben duoi
        $data = $request->all();
        if($id != $data["id"]){
            return response()->json(["error" => "bad request id"]);
        }
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
            //id da hop le roi tiep den tim category theo id
         // di tu Model can lam viec goi ra query de co the su dung
        // cac cau lenh query trong database tren model 

        //query co bang thi no da co du lieu trong database roi
        //vi du tron database minh co 3 category
        //thi query nay no da co 3 category roi nhung no khac voi array
        //no co them nhung ham de query them trong database nhu where select
        // $category = $this->_unitOfWork->category()->get("id = $id");
        $query = Category::query();
                //tu query goi la ham whereRam va truyen dieu kien nhu ben duoi
        //de tim kiem category theo id
        //roi goi thang first de lay thang dau tien
        $category = $query->whereRaw("id = $id")->first();
        // $category = $this->_unitOfWork->category()->get("id = $id");
     //validate category co ton tai hay khong
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    

        try {
            // $this->_unitOfWork->category()->update($data);
            //update category 
            $category->update($data);
            return response()->json(['success' => true, 'message' => 'Category updated']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }

    public function delete(?int $id){
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id == 0) {
            return response()->json(["error" => "Id is not found"]);
        }
    
    
        // $category = $this->_unitOfWork->category()->get("id = $id");
         //id da hop le roi tiep den tim category theo id
         // di tu Model can lam viec goi ra query de co the su dung
        // cac cau lenh query trong database tren model 

        //query co bang thi no da co du lieu trong database roi
        //vi du tron database minh co 3 category
        //thi query nay no da co 3 category roi nhung no khac voi array
        //no co them nhung ham de query them trong database nhu where select
        // $category = $this->_unitOfWork->category()->get("id = $id");
        $query = Category::query();
        //tu query goi la ham whereRam va truyen dieu kien nhu ben duoi
        //de tim kiem category theo id
        //roi goi thang first de lay thang dau tien
        $category = $query->whereRaw("id = $id")->first();
                //validate category co ton tai hay khong
        if ($category == null) {
            return response()->json(["error" => "Category not found"]);
        }
    
        try {
            //xoa category
            // $this->_unitOfWork->category()->delete($category);
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
        }
    }
}

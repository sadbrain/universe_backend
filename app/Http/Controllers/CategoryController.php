<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;

class CategoryController extends ApiController
{  
   
    public function getAll(){
        //phản hồi sẽ trả về 
        //200 là thành công
        $response = [
            'status_code' => 200,
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
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
        $response["data"] = $categories;
        // $categories = $this->_unitOfWork->category()->get_all()->get()->all();
        return response()->json($response);

    }

    public function get(?int $id)
    {
        //phản hồi sẽ trả về 
        //200 là thành công
        $response = [
            'status_code' => 200,
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id <= 0) {
            //tình trạng trả về và message error for this case
            $response["error_messages"] = 'Invalid id';
            $response["status_code"] = '400';
            return response()->json($response);
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
            //tình trạng trả về và message error for this case
            $response["error_messages"] = 'Category not found';
            $response["status_code"] = '404';
            return response()->json($response);
        }

        $response["data"] = $category;
        return response()->json($response);

    }

    public function create(Request $request){
        //tạo thành công sẽ là 201
        $response = [
            'status_code' => 201,
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        //lay du lieu muon tao tu request nhu ben duoi và validate
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {

            // $this->_unitOfWork->category()->add($data);
            //tao category
            Category::create($data);
            $response["success_messages"] = 'Category created';
            return response()->json($response);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            $response["status_code"] = 500;
            return response()->json($response);
        }
    }
    
    public function update(Request $request,int $id){
        $response = [
            'status_code' => 200,
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        //lay du lieu muon tao tu request nhu ben duoi
        $data = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);
        
        //lay du lieu muon tao tu request nhu ben duoi
        if($id != $data["id"]){
            $response["error_messages"] = 'Bad request id';
            $response["status_code"] = '400';
            return response()->json($response);
        }
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            $response["status_code"] = '400';
            return response()->json($response);
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
            $response["error_messages"] = 'Category not found';
            $response["status_code"] = '404';
            return response()->json($response);
        }
    

        try {
            // $this->_unitOfWork->category()->update($data);
            //update category 
            $category->update($data);
            $response["success_messages"] = 'Category updated';
            return response()->json($response);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            $response["status_code"] = 500;
            return response()->json($response);
        }
    }

    public function delete(?int $id){
        //lay theo id
        //validate id khong duoc null voi bang 0
        $response = [
            'status_code' => 204,
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            $response["status_code"] = '400';
            return response()->json($response);
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
            $response["error_messages"] = 'Category not found';
            $response["status_code"] = '404';
            return response()->json($response);
        }
    
        try {
            //xoa category
            // $this->_unitOfWork->category()->delete($category);
            $category->delete();
            $response["success_messages"] = 'Category deleted';
            return response()->json($response);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            $response["status_code"] = 500;
            return response()->json($response);
        }
    }
}

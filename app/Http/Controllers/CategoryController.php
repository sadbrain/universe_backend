<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Validator;

class CategoryController extends ApiController
{  

    public function getAll(Request $request){
        //phản hồi sẽ trả về 
        //200 là thành công
        $response = [
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
        return response()->json($response, 200);

    }

    public function get(Request $request,int $id)
    {
        //phản hồi sẽ trả về 
        //200 là thành công
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);
        if(!$this->isAdmin($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id <= 0) {
            //tình trạng trả về và message error for this case
            $response["error_messages"] = 'Invalid id';
            return response()->json($response, 400);
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
            return response()->json($response, 404);
        }

        $response["data"] = $category;
        return response()->json($response, 200);

    }

    public function create(Request $request){
        //tạo thành công sẽ là 201
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);
        if(!$this->isAdmin($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        try {

            // $this->_unitOfWork->category()->add($data);
            //tao category
            Category::create($validator->validated());
            $response["success_messages"] = 'Category is created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
    
    public function update(Request $request,int $id){
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);
        if(!$this->isAdmin($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        
        //lay du lieu muon tao tu request nhu ben duoi
        if($id != $request->input("id")){
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }
        //lay theo id
        //validate id khong duoc null voi bang 0
        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
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
            $response["error_messages"] = 'Category is not found';
            return response()->json($response, 404);
        }
    

        try {
            // $this->_unitOfWork->category()->update($data);
            //update category 
            $category->update($validator->validated());
            $response["success_messages"] = 'Category is updated successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, ?int $id){
        //lay theo id
        //validate id khong duoc null voi bang 0
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);
        if(!$this->isAdmin($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }
        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
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
            return response()->json($response, 404);
        }
        try {
            //xoa category
            // $this->_unitOfWork->category()->delete($category);
            $category->delete();
            $response["success_messages"] = 'Category deleted';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
}

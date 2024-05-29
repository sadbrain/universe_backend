<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Hamcrest\Type\IsNumeric;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\RefundCreateOptions;
use Stripe\Stripe;
use Validator;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Exception;
class ProductController extends ApiController
{

    public function getProductsByCategory(?int $id = null, int $page = 1){
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        
        if ($id == null || !is_numeric($id)) {
            $firstCategory = $this->_unitOfWork->category()->get_all()->first();
            $id = $firstCategory->id;   
        }   
        $perPage = 10;
        $products = $this->_unitOfWork->product()->get_all("category_id = $id")
            ->paginate($perPage, ["*"], "page", $page)->items();
        foreach ($products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
            $response["data"][] = $product;
        }

        return response()->json($response, 200);
    }
   
    public function getAll(Request $request){
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


        $products = $this->_unitOfWork->product()->get_all()->get()->all();
        foreach ($products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
            $response["data"][] = $product;
        }
        return response()->json($response, 200);
    }

    public function create(Request $request){
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
            'product.name' => 'required|string|min:2|max:255',
            'product.description' => 'nullable|string',
            'product.price' => 'required|numeric|min:1',
            'inventory.quantity' => 'required|numeric|min:1',
            'inventory.quantity_sold' => 'nullable|numeric|min:1',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:255|required_with:sizes.*', 
            'sizes.*.quantity' => 'nullable|integer|min:1|required_with:sizes.*',
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|min:2|max:255|required_with:colors.*', 
            'colors.*.quantity' => 'nullable|integer|min:1|required_with:colors.*', 
            'discount.price' => 'required|numeric|min:1',
            'discount.start_date' => 'required|date',
            'discount.end_date' => 'required|date|after:discount.start_date',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }



        try {
            $product = $this->_unitOfWork->product()->add($request->input("product"));
            $inventory = $this->_unitOfWork->inventory()->add($request->input("inventory"));
            $discount = $this->_unitOfWork->discount()->add($request->input("discount"));
            $product->inventory_id = $inventory->id;
            $product->discount_id = $discount->id;
            $this->_unitOfWork->product()->update($product);
    
    
            $sizes = $request->input("sizes");
            $colors = $request->input("colors");
            foreach($sizes as $size){
                $product_size = new \App\Models\ProductSize();
                $product_size->fill($size);
                $product_size->inventory_id = $inventory->id;
                $this->_unitOfWork->product_size()->add($product_size);

            }
    
            foreach($colors as $color){
                $product_color = new \App\Models\ProductColor();
                $product_color->fill($color);
                $product_color->inventory_id = $inventory->id;
                $this->_unitOfWork->product_color()->add($product_color);

            }
        
            if($request->has("image")){
                $file = $request->get("image");
                $originalFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();  
                $filename = time() . '_' . pathinfo($originalFilename, PATHINFO_FILENAME) . '.' . $extension;
                $foldername = "/images/product/product-".$product->id;
                $folderpath  = public_path($foldername);

                if(!file_exists($folderpath)){
                    mkdir($folderpath, 0777, true);
                }

                $file->move($folderpath, $filename);
                $product -> thumbnail = $foldername ."/". $filename;
                $this->_unitOfWork->product()->update($product);

            }

            $response["success_messages"] = 'Product created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
    public function update(Request $request, int $id){
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
            'product.name' => 'required|string|min:2|max:255',
            'product.description' => 'nullable|string',
            'product.price' => 'required|numeric|min:1',
            'inventory.quantity' => 'required|integer|min:1',
            'inventory.quantity_sold' => 'nullable|integer|min:1',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:255|required_with:sizes.*', 
            'sizes.*.quantity' => 'nullable|integer|min:1|required_with:sizes.*',
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|min:2|max:255|required_with:colors.*', 
            'colors.*.quantity' => 'nullable|integer|min:1|required_with:colors.*', 
            'discount.price' => 'required|numeric|min:1',
            'discount.start_date' => 'required|date',
            'discount.end_date' => 'required|date|after:discount.start_date',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        if($id != $request->input("product.id")){
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }
            
        $product = $this->_unitOfWork->product()->get("id = $id");
        if ($product == null) {
            $response["error_messages"] = 'Product not found';
            return response()->json($response, 404);
        }

        try {
            $inventory = $this->_unitOfWork->inventory()->get("id = $product->inventory_id");
            $discount = $this->_unitOfWork->discount()->get("id = $product->discount_id");
            $product->fill($request->input("product"));
            $discount->fill($request->input("discount"));
            $inventory->fill($request->input("inventory"));
            $this->_unitOfWork->product()->update($product);
            $this->_unitOfWork->inventory()->update($inventory);
            $this->_unitOfWork->discount()->update($discount);
            
            $sizes = $request->input("sizes");
            $colors = $request->input("colors");
            foreach($sizes as $size){
                $product_size = $this->_unitOfWork->product_size()->get("id =". $size["id"]);
                $product_size->fill($size);
                $this->_unitOfWork->product_size()->update($product_size);

            }
    
            foreach($colors as $color){
                $product_color = $this->_unitOfWork->product_color()->get("id =". $color["id"]);
                $product_color->fill($color);
                $this->_unitOfWork->product_color()->update($product_color);

            }

            if($request->has("image")){
                $file = $request->get("image");
                $originalFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();  
                $filename = time() . '_' . pathinfo($originalFilename, PATHINFO_FILENAME) . '.' . $extension;
                $foldername = "/images/product/product-".$product->id;
                $folderpath  = public_path($foldername);

                if(!file_exists($folderpath)){
                    mkdir($folderpath, 0777, true);
                }else{
                    $existingFiles = File::files($folderpath);
                    foreach ($existingFiles as $existingFile) {
                        File::delete($existingFile);
                    }
                }

                $file->move($folderpath, $filename);
                $product -> thumbnail = $foldername ."/". $filename;
                $this->_unitOfWork->product()->update($product);

            }

            $response["success_messages"] = 'Product updated successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function delete(Request $request, ?int $id = null){
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
            
        $product = $this->_unitOfWork->product()->get("id = $id");
        if ($product == null) {
            $response["error_messages"] = 'Product not found';
           
            return response()->json($response, 404);
        }
        try {
            $this->_unitOfWork->product()->delete($product);
            $foldername = "/images/product/product-".$product->id;
            $folderpath  = public_path($foldername);
            if(file_exists($folderpath)){
                $existingFiles = File::files($folderpath);
                foreach ($existingFiles as $existingFile) {
                    File::delete($existingFile);
                }
                File::deleteDirectory($folderpath);
            }

            $response["success_messages"] = 'Product deleted successfully';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }

    }

    public function createSizeMore(Request $request, int $inventory_id){
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
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:255|required_with:sizes.*', 
            'sizes.*.quantity' => 'nullable|integer|min:1|required_with:sizes.*',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        
        try {
    
            $sizes = $request->input("sizes");
            foreach($sizes as $size){
                $product_size = new \App\Models\ProductSize();
                $product_size->fill($size);
                $product_size->inventory_id = $inventory_id;
                $this->_unitOfWork->product_size()->add($product_size);

            }
    
            $response["success_messages"] = 'Size is created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }


    public function createColorMore(Request $request, int $inventory_id){
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
            'colors' => 'nullable|array',
            'colors.*.name' => 'nullable|string|min:2|max:255|required_with:colors.*', 
            'colors.*.quantity' => 'nullable|integer|min:1|required_with:colors.*', 
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        
        try {
            $colors = $request->input("colors");
            foreach($colors as $color){
                $product_color = new \App\Models\ProductColor();
                $product_color->fill($color);
                $product_color->inventory_id = $inventory_id;
                $this->_unitOfWork->product_color()->add($product_color);

            }
    
            $response["success_messages"] = 'Colors is created successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function deleteSizeMore(Request $request, int $id){
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

        $product_size = $this->_unitOfWork->product_size()->get("id = $id");
        if ($product_size == null) {
            $response["error_messages"] = 'Product size not found';
           
            return response()->json($response, 404);
        }
        try {
            $this->_unitOfWork->product_size()->delete($product_size);
            $response["success_messages"] = 'Product size deleted successfully';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function deleteColorMore(Request $request, int $id){
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

        $product_color = $this->_unitOfWork->product_color()->get("id = $id");
        if ($product_color == null) {
            $response["error_messages"] = 'Product color not found';
           
            return response()->json($response, 404);
        }
        try {
            $this->_unitOfWork->product_color()->delete($product_color);
            $response["success_messages"] = 'Product color deleted successfully';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }


    public function getBestSellProducts(){
         $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
      
        $top_selling_products = $this->_unitOfWork->order_detail()->get_best_seller_products();
        foreach ($top_selling_products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
        }
        $response["data"] = $top_selling_products;

        return response()->json($response, 200);
      }
  
    public function getBestRatingProducts(){

        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $top_rating_products = $this->_unitOfWork->product()->get_best_rating_products();
        foreach ($top_rating_products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
        }
        $response["data"] = $top_rating_products;
      
        return response()->json($response, 200);
    }
    public function getProductsByPrice(Request $request,$price = null,  int $page = 1){
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        if ($price == null){
            $price = 100;
        }
        $perPage = 10;
        $products = $this->_unitOfWork->product()->get_all("price <= $price")
            ->paginate($perPage, ["*"], "page", $page)->items();
        foreach ($products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
            $response["data"][] = $product;
        }

        return response()->json($response, 200);
    }
    public function getProduct($id){
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
 
        if ($id == null || $id <= 0) {
            $response["error_messages"] = 'Invalid id';
            return response()->json($response, 400);
        }
        $query = Product::query();
        $product = $query->whereRaw("id = $id")->first();

        if ($product == null) {
            $response["error_messages"] = 'product not found';
            return response()->json($response, 404);
        }
        $product->category;
        $product->discount;
        $product->inventory->sizes;
        $product->inventory->colors;
        $response["data"] = $product;

        return response()->json($response, 200);
    }
    public function getRelatedProducts($id){
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        
        $products = $this->_unitOfWork->product()->get_all("category_id = $id")->get()->all();
        
        foreach ($products as $product){
            $product->inventory;
            $product->discount;
            $product->category;     
            $response["data"][] = $product;
        }

        return response()->json($response, 200);   
    }
}

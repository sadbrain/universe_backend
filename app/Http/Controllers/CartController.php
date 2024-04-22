<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use JWTAuth;
use Validator;


class CartController extends ApiController
{
    public function showCart(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);

        $query = ShoppingCart::query();
        $carts = $query->whereRaw("user_id = $user->id")->get()->all();

        $response["data"] = $carts;
        return response()->json($response, 200);
    }

    public function addToCart(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);

        $validator = Validator::make($request->all(), [
            'color' => 'required|string|min:1|max:255',
            'size' => 'required|string|min:1|max:255',
            'quantity' => 'required|numeric|min:1',
            'product_id' => 'required|numeric|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {

            $cart = new ShoppingCart();
            $cart->fill($validator->validated());
            $cart->user_id = $user->id;
            $cart->save(); 

            $response["success_messages"] = 'Add to cart successfully';
            return response()->json($response, 201);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }

    public function minus(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        $cart = ShoppingCart::query()->whereRaw("id = $id")->first();

        if ($cart == null) {
            $response["error_messages"] = 'Cart is not found';
            return response()->json($response, 404);
        }

        try {
            if($cart->quantity>1) $cart->decrement('quantity');
            $response["success_messages"] = 'Cart quantity is decreased successfully';
            return response()->json($response, 200);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
    public function plus(Request $request, int $id)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        $cart = ShoppingCart::query()->whereRaw("id = $id")->first();
        if ($cart == null) {
            $response["error_messages"] = 'cart is not found';
            return response()->json($response, 404);
        }

        try {
            $cart->increment('quantity');
            $response["success_messages"] = 'cart quantity is increased successfully';
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
        if ($id == null || $id == 0) {
            $response["error_messages"] = 'Bad request id';
            return response()->json($response, 400);
        }

        $query = ShoppingCart::query();
   
        $cart = $query->whereRaw("id = $id")->first();
        if ($cart == null) {
            $response["error_messages"] = 'Cart not found';
            return response()->json($response, 404);
        }
        
        try {
            $cart->delete();
            $response["success_messages"] = 'Cart is deleted successfully';
            return response()->json($response, 204);
        } catch (Exception $e) {
            $response["error_messages"] = 'Exception: ' . $e->getMessage();
            return response()->json($response, 500);
        }
    }
}

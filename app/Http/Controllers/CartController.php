<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use JWTAuth;
use Validator;


class CartController extends ApiController
{  
    public function addToCart(Request $request){
        $validator = Validator::make($request->all(), [
            'color' => 'required|string',
            'quantity' => 'required|numeric|min:1',
            'size' => 'required|string',
            'product_id' => 'nullable',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $cart = new \App\Models\ShoppingCart();
        $cart->fill($validator->validated());

        $user = $this->getUser($request);
        $cart->user_id = $user->id;
        $cartInDb = $this->_unitOfWork->cart()->get("user_id = $user->id and product_id = $cart->product_id");
        if($cartInDb != null){
            $cartInDb->fill($cart->toArray());
            $this->_unitOfWork->cart()->update($cartInDb);

        }else{  
            $this->_unitOfWork->cart()->add($cart);
        }
        return response()-> json([
            'success_messages' => 'Cart is added successfully',
            'cart' => $cart
        ], 201);

    }   

    public function plus(int $id){
        $cart = $this->_unitOfWork->cart()->get("id = $id");
        $cart->quantity+=1;
        $this->_unitOfWork->cart()->update($cart);
        return response()-> json([
            'success_messages' => 'Cart is updated successfully',
            'cart' => $cart
        ], 201);

    }   
    public function minus(int $id){
        $cart = $this->_unitOfWork->cart()->get("id = $id");
        if($cart->quantity > 1) $cart->quantity-=1;
        $this->_unitOfWork->cart()->update($cart);
        return response()-> json([
            'success_messages' => 'Cart is updated successfully',
            'cart' => $cart
        ], 201);

    }   

    public function delete(int $id){
        $cart = $this->_unitOfWork->cart()->get("id = $id");
        $this->_unitOfWork->cart()->delete($cart);
        return response()-> json([
            'success_messages' => 'Cart is deleted successfully',
            'cart' => $cart
        ], 201);
    }   


    public function showCart(Request $request){
        $user = $this->getUser($request);
        $carts = $this->_unitOfWork->cart()->get_all("user_id = $user->id")->get()->all(); 
        foreach($carts as $c){
            $c -> product;
        }
        return response()->json(["data" => $carts]);
    }
    

    public function summary(Request $request){
        $user = $this->getUser($request);
        $order = new \App\Models\Order();
        $order->fill($request->input('order'));
        $order->user_id = $user->id;
        $order->order_date = now();


        $cart_ids = $request->input('cart_id');
        $shopping_carts = $this->_unitOfWork->cart()->get_all("user_id = $user->id");
        $shopping_carts = $shopping_carts->whereIn('id', $cart_ids)->get()->all();

        foreach ($shopping_carts as $cart){
            $product = $this->_unitOfWork->product()->get("id = $cart->product_id");
            $discount = $this->_unitOfWork->discount()->get("id = $product->discount_id");

            if(strtotime($discount->end_date) >= time()){
                $price = $product->price - ($product->price * $discount->price / 100);
            }else{
                $price = $product ->price;
            }
            $order->order_total += $price * $cart->quantity;
            
        }

        $order_status = config('constants.order_status');
        $payment_status = config('constants.payment_status');
        $payment = new \App\Models\Payment();


        if($user->company_id != null){
            $order->order_status = $order_status["approved"];
            $payment->payment_status = $payment_status["delayed_payment"];
        }
        //custoemr is normal account
        else{
            $order->order_status = $order_status["pending"];
            $payment->payment_status = $payment_status["pending"];
        }

        $order = $this->_unitOfWork->order()->add($order);
        $payment->order_id = $order->id;
        $payment->user_id = $user->id;
        $this->_unitOfWork->payment()->add($payment);

        foreach ($shopping_carts as $cart){
            $product = $this->_unitOfWork->product()->get("id = $cart->product_id");
            $discount = $product->discount;
            if(strtotime($discount->end_date) >= time()){
                $price = $product -> price - ($product->price * $discount->price / 100);
            }else{
                $price = $product -> price;
            }

            $order_detail = [
                "quantity" => $cart->quantity,
                "product_id" => $cart->product_id,
                'color' => $cart->color,
                'size' => $cart->size,
                "price" => $price,
                "order_id" => $order->id
            ];
            $this->_unitOfWork->order_detail()->add($order_detail);
        }
        $frontend_domain = config("constants.frontend_domain");
        if($user->company_id == null){
            Stripe::setApiKey(config('stripe.sk'));
            $lineItems = [];
            foreach ($shopping_carts as $cart){
                $product = $this->_unitOfWork->product()->get("id = $cart->product_id");
                $discount = $product->discount;
                if(strtotime($discount->end_date) >= time()){
                    $price = $product -> price - ($product->price * $discount->price / 100);
                }else{
                    $price = $product -> price;
                }
    
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $cart->product->name,
                        ],
                        'unit_amount' => (int)($price * 100), // $20.50 => 2050
                    ],
                    'quantity' => $cart->quantity,
                ];
            }
            $session = Session::create([
                'success_url' => $frontend_domain . "/orderConfirmation?order_id=$order->id",
                // 'cancel_url' => $domain . "/v1/cart/index",
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
            ]);

            $this->_unitOfWork->order()->update_stripe_payment_id($order->id, $session->id , $session->payment_intent);
            return response()->json(['success_url' => $session->url]);
        }

        //return view xác nhận thành công của frontend
        return response()->json(['success_url' => "/orderConfirmation?order_id=$order->id"]);

    }

    public function orderConfirmation(int $id){
        $order_status = config('constants.order_status');
        $payment_status = config('constants.payment_status');
        $order = $this->_unitOfWork->order()->get("id = $id");
        $payment = $this->_unitOfWork->payment()->get("order_id = $order->id");
        if($payment->session_id == null)
        {
            return response()->json(['order' => $order, 'success_message' => 'Order placed successfully', 'order_id' => $order->id], 200);
        }
        Stripe::setApiKey(config('stripe.sk'));
        $session = Session::retrieve($payment->session_id);
        if($payment->payment_status !== $payment_status["delayed_payment"])
        {
            // Place an order by customer   
            if (strtolower($session->payment_status) == 'paid') {
                $this->_unitOfWork->order() ->update_status($order->id, $order_status["approved"], $payment_status["approved"]);
                $this->_unitOfWork->order() ->update_stripe_payment_id($order->id, $session->id , $session->payment_intent);
                return response()->json(['order' => $order, 'success_message' => 'Order placed successfully', 'order_id' => $order->id], 200);
            
            }
            return response()->json(['order' => $order, 'error_message' => 'Order don"t placed successfully', 'order_id' => $order->id], 200);

        }
        else if($payment->payment_status === $payment_status["delayed_payment"]){
  

            if (strtolower($session->payment_status) == 'paid') {
                $this->_unitOfWork->order()->update_status($order->id, $order->order_status, $payment_status["approved"]);
                $this->_unitOfWork->order()->update_stripe_payment_id($order->id, $session->id , $session->payment_intent);
                return response()->json(['order' => $order, 'success_message' => 'payment is successfully', 'order_id' => $order->id], 200);

            }
            return response()->json(['order' => $order, 'error_message' => 'payment isn"t successfully', 'order_id' => $order->id], 200);

        }

    }

    public function showCartForSumary(Request $request){
        $user = $this->getUser($request);
        $carts = $this->_unitOfWork->cart()->get_all("user_id = $user->id");
        $carts = $carts->whereIn("id", $request->cart_id)->get()->all(); 
        foreach($carts as $c){
            $c -> product -> discount;
        }
        return response()->json(["data" => $carts]);
    }

}

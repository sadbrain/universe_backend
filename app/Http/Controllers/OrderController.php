<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\RefundCreateOptions;
use Stripe\Stripe;
use Validator;
use Carbon\Carbon;

class OrderController extends ApiController
{
    public function getAllOfAdmin(Request $request, string $status)
    {
        $user = $this->getUser($request);
        if(!$this->isAdmin($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];

        $order_status = config("constants.order_status");
        $payment_status = config("constants.payment_status");
        $orders = $this->_unitOfWork->order()->get_all();
        switch ($status) {
            case "pending":
                $orders_pending_ids = [];
                $payments = $this->_unitOfWork->payment()->get_all("payment_status = '{$payment_status['delayed_payment']}'")->get()->all();
                foreach ($payments as $payment) {
                    $orders_pending_ids[] = $payment->order_id;
                }
                $orders = $orders->whereIn('id', $orders_pending_ids);
                break;
            case "inprocess":
                $orders = $orders->whereRaw("order_status = '{$order_status['in_process']}'");
                break;
            case "completed":
                $orders = $orders->whereRaw("order_status = '{$order_status['shipped']}'");
                break;
            case "approved":
                $orders = $orders->whereRaw("order_status = '{$order_status['approved']}'");
                break;
        }
        $response["data"] = $orders->get()->all();
        return response()->json($response, 200);
    }

    public function detail(Request $request, int $id)
    {  

        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $order = $this->_unitOfWork->order()->get("id = $id");
        $order_details = $this->_unitOfWork->order_detail()->get_all("order_id = $id")->get()->all();
        $payment = $this->_unitOfWork->payment()->get("order_id = $id");

        $obj = [
            "order" => $order,
            "order_details" => $order_details,
            "payment" => $payment,
        ];
        
        array_push($response["data"], $obj);
        return response()->json($response, 200);
    }

    public function detailPost(Request $request)
    {

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
            'phone' => 'required|string|min:10|max:20',
            'street_address' => 'required|string|min:6|max:255',
            'district_address' => 'required|string|min:6|max:255',
            'city' => 'required|string|min:6|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }


        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }
        $order->fill($validator->validated());
        if(filled($request->input("carrier")))  $order->carrier=$request->input("carrier");
        if(filled($request->input("tracking_number")))  $order->tracking_number=$request->input("tracking_number");
        $this->_unitOfWork->order()->update($order);

        $response["success_messages"] = "Order Details Updated Successfully";
        return response()->json($response, 200);
    }

    public function startProcessing(Request $request)
    {
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

        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }

        $response["success_messages"] = "Order Details Updated Successfully.";
        $this->_unitOfWork->order()->update_status($orderId, config("constants.order_status.in_process"));
        return response()->json($response, 200);
    }
    public function shipOrder(Request $request)
    {
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

        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }
        $validator = Validator::make($request->all(), [
            'carrier' => 'required|string|min:6|max:255',
            'tracking_number' => 'required|string|min:6|max:255',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $order->carrier = $request->input("order.carrier");
        $order->order_status = config("constants.order_status.shipped");
        $order->tracking_number = $request->input("order.tracking_number");
        $order->shipping_date = now();
        $payment = $this->_unitOfWork->payment()->get("order_id = $orderId");
        if ($payment->payment_status == config("constants.payment_status.delayed_payment")) {
            $payment->payment_due_date = now()->addDays(30);
        }
        $this->_unitOfWork->order()->update($order);
        $this->_unitOfWork->payment()->update($payment);

        $response["success_messages"] = "Order Shipped Successfully.";
        return response()->json($response, 200);
    }

    public function paynow(Request $request)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];  
        $user = $this->getUser($request);
        if(!$this->isCompany($user)){
            $response["error_messages"] = "You do not have permission to access this page.";
            return response()->json($response, 403);
        }

        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }
        $order_details = $this->_unitOfWork->order_detail()->get_all("order_id = $orderId")->get()->all();

        $frontend_domain = config("constants.frontend_domain");
        Stripe::setApiKey(config('stripe.sk'));
        $lineItems = [];
        foreach ($order_details as $detail) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $detail->product->name,
                    ],
                    'unit_amount' => (int)($detail->price * 100), // $20.50 => 2050
                ],
                'quantity' => $detail->quantity,
            ];
        }
        $session = Session::create([
            'success_url' => $frontend_domain . "/orderConfirmation.html?order_id=$order->id",
            // 'cancel_url' => $domain . "/admin//order/detail/$order->id",
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
        ]);

        $this->_unitOfWork->order()->update_stripe_payment_id($order->id, $session->id, $session->payment_intent);
        $response['success_url'] = $session->url;
        return response()->json($response);
    }
    
}

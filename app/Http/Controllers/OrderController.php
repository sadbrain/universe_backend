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
    public function cancelOrder(Request $request)
    {
        $order_status = config('constants.order_status');
        $payment_status = config('constants.payment_status');
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }

        $payment = $this->_unitOfWork->payment()->get("order_id = $orderId");
        if ($payment->payment_status == $payment_status["approved"]) {
            Stripe::setApiKey(config('stripe.sk'));
            $options = [
                'reason' => "requested_by_customer",
                'payment_intent' => $payment->payment_intent_id,
            ];
            $refund = Refund::create($options);
            $this->_unitOfWork->order()->update_status($order->id, $order_status["cancelled"], $order_status["refunded"]);
        } else {
            $this->_unitOfWork->order()->update_status($order->id, $order_status["cancelled"], $order_status["cancelled"]);
        }
        $response["success_messages"] = "Order Cancelled Successfully.";
        return response()->json($response, 200);
    }
}

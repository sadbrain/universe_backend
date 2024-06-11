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
        $response["data"] = $orders->with('user')->get()->all();
        return response()->json($response, 200);
    }
    public function getAllOfCustomers(Request $request, string $status)
    {
        $response = [
            'data' => [],
            'error_messages' => '',
            'success_messages' => '',
        ];
        $user = $this->getUser($request);
        $order_status = config("constants.order_status");
        $orders = $this->_unitOfWork->order()->get_all("user_id = $user->id");
        switch ($status) {
            case "ordering":
                $orders = $orders->whereRaw("order_status IN ('{$order_status['pending']}', '{$order_status['approved']}', '{$order_status['in_process']}')");
                break;
            case "ordered":
                $orders = $orders->whereRaw("order_status = '{$order_status['shipped']}'");
                break;
            case "cancelled":
                $orders = $orders->whereRaw("order_status = '{$order_status['cancelled']}'");
                break;
        }
        $orders = $orders->get()->all();
        foreach ($orders as $o) {
            $order_details = $this->_unitOfWork->order_detail()->get_all("order_id = $o->id")->get()->all();
            $payment = $this->_unitOfWork->payment()->get("order_id =" . $o->id);
            $o["payment"] = $payment;
            $o["order_details"] = $order_details;
        }
        $response["data"] = $orders;
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
        $order->user;
        $obj = [
            "order" => $order,
            "order_details" => $order_details,
            "payment" => $payment,
        ];
        foreach ($order_details as $o) {
            $o->product;
        }
        array_push($response["data"], $obj);
        return response()->json($response, 200);
    }

    public function detailPost(Request $request, int $id)
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
            'phone' => 'required|string|min:10|max:20',
            'street_address' => 'required|string|min:6|max:255',
            'district_address' => 'required|string|min:6|max:255',
            'city' => 'required|string|min:6|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $orderId = $request->input("order.id"); // Assuming the order ID is in the "id" field of the "order" input
        $order = $this->_unitOfWork->order()->get("id = $orderId");
        if ($order == null) {
            $response["error_messages"] = 'Order not found';
            return response()->json($response, 404);
        }
        $order->fill($validator->validated());
        if (filled($request->input("carrier")))  $order->carrier = $request->input("carrier");
        if (filled($request->input("tracking_number")))  $order->tracking_number = $request->input("tracking_number");
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
        if (!$this->isAdmin($user)) {
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
        if (!$this->isAdmin($user)) {
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
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $order->carrier = $request->input("carrier");
        $order->order_status = config("constants.order_status.shipped");
        $order->tracking_number = $request->input("tracking_number");
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
        if (!$this->isCompany($user)) {
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
            'success_url' => $frontend_domain . "/orderConfirmation?order_id=$order->id",
            // 'cancel_url' => $domain . "/admin//order/detail/$order->id",
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
        ]);

        $this->_unitOfWork->order()->update_stripe_payment_id($order->id, $session->id, $session->payment_intent);
        $response['success_url'] = $session->url;
        return response()->json($response, 200);
    }
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

    public function getDailyOrders(Request $request)
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

        $order_chart = "Daily order quantity statistics";
        $count_orders = [];
        $label = [];

        $first_date_of_month = Carbon::now()->startOfMonth();
        $last_date_of_month = Carbon::now()->endOfMonth();
        for ($day = $first_date_of_month; $day <= $last_date_of_month; $day->addDay()) {

            $start_of_day = Carbon::createFromFormat('Y-m-d H:i:s', $day->startOfDay());
            $end_of_day = Carbon::createFromFormat('Y-m-d H:i:s', $day)->endOfDay();
            $num = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
            $count_orders[] = $num;
            $label[] = $day->day;
        }
        $response["data"][] = [
            "order_count" => $count_orders,
            "order_chart" => $order_chart,
            "label" => $label,
        ];
        return response()->json($response, 200);
    }

    public function getMonthlyOrders(Request $request)
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

        $order_chart = "Monthly order quantity statistics";
        $count_orders = [];
        $label = [];
        $current_date = Carbon::now();

        for ($year = 1; $year <= 12; $year++) {

            $current_month =  Carbon::createFromFormat('Y-m-d H:i:s', "$current_date->year-$year-$current_date->day 00:00:00");
            $start_of_day =  Carbon::createFromFormat('Y-m-d H:i:s', $current_month->startOfMonth());
            $end_of_day =  Carbon::createFromFormat('Y-m-d H:i:s', $current_month->endOfMonth());
            $num = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
            $count_orders[] = $num;
            $label[] = $year;
        }
        $response["data"][] = [
            "order_count" => $count_orders,
            "order_chart" => $order_chart,
            "label" => $label,
        ];
        return response()->json($response, 200);
    }

    public function getYearlyOrders(Request $request)
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

        $order_chart = "Yearly order quantity statistics";
        $count_orders = [];
        $label = [];
        $start_date = Carbon::createFromFormat('Y-m-d H:i:s', config("constants.start_date"));
        $current_date = Carbon::now();

        for ($year = $start_date->year; $year <= $current_date->year; $year++) {
            $current_year =  Carbon::createFromFormat('Y-m-d H:i:s', "$year-01-01 00:00:00");
            $start_of_day =  Carbon::createFromFormat('Y-m-d H:i:s', $current_year->startOfYear());
            $end_of_day =  Carbon::createFromFormat('Y-m-d H:i:s', $current_year->endOfYear());
            $num = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
            $count_orders[] = $num;
            $label[] = $year;
        }
        $response["data"][] = [
            "order_count" => $count_orders,
            "order_chart" => $order_chart,
            "label" => $label,
        ];
        return response()->json($response, 200);
    }

    public function getTotalRevenueOrder(Request $request)
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


        $start_of_day = Carbon::createFromFormat('Y-m-d H:i:s', config("constants.start_date"));
        $end_of_day = Carbon::now()->endOfDay();
        $order_total = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
        $revenue_total = $this->_unitOfWork->order()->get_total_revenue_order($start_of_day, $end_of_day);

        $response["data"][] = [
            "order_total" => $order_total,
            "revenue_total" => $revenue_total,
        ];
        return response()->json($response, 200);
    }

    public function getCurrentYearTotalRevenueOrder(Request $request)
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


        $start_of_day = Carbon::now()->startOfYear();
        $end_of_day = Carbon::now()->endOfDay();
        $order_total = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
        $revenue_total = $this->_unitOfWork->order()->get_total_revenue_order($start_of_day, $end_of_day);

        $response["data"][] = [
            "order_total" => $order_total,
            "revenue_total" => $revenue_total,
        ];
        return response()->json($response, 200);
    }
}

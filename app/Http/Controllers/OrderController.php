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

    public function getDailyOrders(Request $request){
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
        $response["data"][] = ["order_total" => $count_orders,
                                "order_chart" => $order_chart,
                                "label" => $label,] ;
        return response()->json($response, 200);
    }

    public function getMonthlyOrders(Request $request){
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
        $response["data"][] = ["order_count" => $count_orders,
                                "order_chart" => $order_chart,
                                "label" => $label,] ;
        return response()->json($response, 200);
    }

    public function getYearlyOrders(Request $request){
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
        $response["data"][] = ["order_count" => $count_orders,
                                "order_chart" => $order_chart,
                                "label" => $label,] ;
        return response()->json($response, 200);
    }

    public function getTotalRevenueOrder(Request $request){
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


        $start_of_day = Carbon::createFromFormat('Y-m-d H:i:s', config("constants.start_date"));
        $end_of_day = Carbon::now()->endOfDay();
        $order_total = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
        $revenue_total = $this->_unitOfWork->order()->get_total_revenue_order($start_of_day, $end_of_day);

        $response["data"][] = ["order_total" => $order_total,
                                "revenue_total" => $revenue_total,
                               ] ;
        return response()->json($response, 200);
    }


    
    public function getCurrentYearTotalRevenueOrder(Request $request){
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


        $start_of_day = Carbon::now()->startOfYear();
        $end_of_day = Carbon::now()->endOfDay();
        $order_total = $this->_unitOfWork->order()->get_num_order($start_of_day, $end_of_day);
        $revenue_total = $this->_unitOfWork->order()->get_total_revenue_order($start_of_day, $end_of_day);

        $response["data"][] = ["order_total" => $order_total,
                                "revenue_total" => $revenue_total,
                               ] ;
        return response()->json($response, 200);
    }
    
}

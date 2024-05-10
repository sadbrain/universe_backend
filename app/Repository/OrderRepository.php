<?php
namespace App\Repository;

use App\Repository\IRepository\IOrderRepository;
use App\Repository\Repository;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;

class OrderRepository extends Repository implements IOrderRepository {
    public function get_model(){
        return \App\Models\Order::class;
    }
    public function update_status(int $id, ?string $order_status, ?string $payment_status = null ){
        $orderFromDb = $this->_model->find($id);
        if($orderFromDb != null){
            $orderFromDb->order_status = $order_status;
            if(filled($payment_status)){
                $payment = new \App\Models\Payment();
                $paymentFromDb = $payment->where("order_id", $orderFromDb->id)->first();
                if($paymentFromDb != null){
                    $paymentFromDb->payment_status = $payment_status;
                    $paymentFromDb->save();
                }
            }
            $orderFromDb->save();
        }
    }
    
    public function update_stripe_payment_id(int $id, string $session_id, ?string $payment_intent_id) {
        $orderFromDb = $this->_model->find($id);
        if($orderFromDb != null){
            $payment = new \App\Models\Payment();
            $paymentFromDb = $payment->where("order_id", $orderFromDb->id)->first();
            $paymentFromDb->session_id = $session_id;
            if(filled($payment_intent_id)){
                $paymentFromDb->payment_intent_id = $payment_intent_id;
                $paymentFromDb->payment_date = Carbon::now();
            }
            $paymentFromDb->save();
        }
    }

    public function get_num_order($start_of_day, $end_of_day){
     
        $count_order = $this->_model::query()
        ->whereBetween('order_date', [$start_of_day, $end_of_day])
        ->where('order_status', config("constants.order_status.shipped"))
        ->count();
    
         return $count_order;
    }

    public function get_total_revenue_order($start_of_day, $end_of_day){
     
        $count_order = $this->_model::query()
        ->whereBetween('order_date', [$start_of_day, $end_of_day])
        ->where('order_status', config("constants.order_status.shipped"))
        ->sum("order_total");
    
         return $count_order;   
    }

}

<?php
namespace App\Repository;

use App\Repository\IRepository\IPaymentRepository;
use App\Repository\Repository;

class PaymentRepository extends Repository implements IPaymentRepository {
    public function get_model(){
        return \App\Models\Payment::class;
    }
}

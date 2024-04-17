<?php
namespace App\Repository\IRepository;

interface IOrderRepository {
    public function update_status(int $id, ?string $order_status, ?string $payment_status = null);
    public function update_stripe_payment_id(int $id, string $session_id, string $payment_intent_id);
}

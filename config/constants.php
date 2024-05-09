<?php

return [
    'role' => [
        'user_cust' => 'Customer',
        'user_comp' => 'Company',
        'user_admin' => 'Admin',
        'user_employee' => 'Employee',
    ],

    'order_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'in_process' => 'Processing',
        'shipped' => 'Shipped',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    'payment_status' => [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'delayed_payment' => 'ApprovedForDelayedPayment',
        'rejected' => 'Rejected',
    ],

    'session' => [
        'cart' => 'SessionShoppingCart',
    ],
    "frontend_domain" => "http://127.0.0.1:5500",
    "start_date" => '2023-10-01 00:00:00',
];
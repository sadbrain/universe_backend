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
];
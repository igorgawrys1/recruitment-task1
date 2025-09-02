<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Patient;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderRepository implements OrderRepositoryInterface
{
    public function findOrCreateByOrderId(string $orderId, Patient $patient): Order
    {
        return Order::firstOrCreate(
            [
                'order_id' => $orderId,
            ],
            [
                'patient_id' => $patient->id,
            ]
        );
    }
}
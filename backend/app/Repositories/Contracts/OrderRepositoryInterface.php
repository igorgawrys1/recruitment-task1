<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\Patient;

interface OrderRepositoryInterface
{
    public function findOrCreateByOrderId(string $orderId, Patient $patient): Order;
}
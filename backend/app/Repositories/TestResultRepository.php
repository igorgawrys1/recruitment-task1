<?php

namespace App\Repositories;

use App\Models\TestResult;
use App\Models\Order;
use App\DTOs\PatientDataDTO;
use App\Repositories\Contracts\TestResultRepositoryInterface;

class TestResultRepository implements TestResultRepositoryInterface
{
    public function createFromDTO(PatientDataDTO $dto, Order $order): TestResult
    {
        return TestResult::create([
            'order_id' => $order->id,
            'name' => $dto->testName,
            'value' => $dto->testValue,
            'reference' => $dto->testReference,
        ]);
    }
}
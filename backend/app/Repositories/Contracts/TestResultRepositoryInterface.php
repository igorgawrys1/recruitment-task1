<?php

namespace App\Repositories\Contracts;

use App\Models\TestResult;
use App\Models\Order;
use App\DTOs\PatientDataDTO;

interface TestResultRepositoryInterface
{
    public function createFromDTO(PatientDataDTO $dto, Order $order): TestResult;
}
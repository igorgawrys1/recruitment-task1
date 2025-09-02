<?php

namespace App\Repositories\Contracts;

use App\Models\Patient;
use App\DTOs\PatientDataDTO;

interface PatientRepositoryInterface
{
    public function findOrCreateFromDTO(PatientDataDTO $dto): Patient;
    
    public function findByCredentials(string $username, string $password): ?Patient;
}
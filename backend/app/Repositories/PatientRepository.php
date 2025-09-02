<?php

namespace App\Repositories;

use App\Models\Patient;
use App\DTOs\PatientDataDTO;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class PatientRepository implements PatientRepositoryInterface
{
    public function findOrCreateFromDTO(PatientDataDTO $dto): Patient
    {
        return Patient::firstOrCreate(
            [
                'name' => $dto->patientName,
                'surname' => $dto->patientSurname,
                'birth_date' => $dto->patientBirthDate,
            ],
            [
                'sex' => $dto->patientSex,
                'password' => Hash::make($dto->patientBirthDate),
            ]
        );
    }

    public function findByCredentials(string $username, string $password): ?Patient
    {
        $parts = $this->parseUsername($username);
        
        if (!$parts) {
            return null;
        }

        $patient = Patient::where('name', $parts['name'])
            ->where('surname', $parts['surname'])
            ->first();

        if ($patient && Hash::check($password, $patient->password)) {
            return $patient;
        }

        return null;
    }

    private function parseUsername(string $username): ?array
    {
        preg_match('/^([A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+)([A-ZĄĆĘŁŃÓŚŹŻ][a-ząćęłńóśźż]+)$/u', $username, $matches);
        
        if (count($matches) !== 3) {
            return null;
        }

        return [
            'name' => $matches[1],
            'surname' => $matches[2],
        ];
    }
}
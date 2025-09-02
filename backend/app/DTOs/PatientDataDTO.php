<?php

namespace App\DTOs;

final class PatientDataDTO
{
    public function __construct(
        public readonly int $patientId,
        public readonly string $patientName,
        public readonly string $patientSurname,
        public readonly string $patientSex,
        public readonly string $patientBirthDate,
        public readonly string $orderId,
        public readonly string $testName,
        public readonly string $testValue,
        public readonly string $testReference
    ) {}

    public static function fromCsvRow(array $row): self
    {
        return new self(
            patientId: (int) $row['patientId'],
            patientName: $row['patientName'],
            patientSurname: $row['patientSurname'],
            patientSex: $row['patientSex'],
            patientBirthDate: $row['patientBirthDate'],
            orderId: $row['orderId'],
            testName: $row['testName'],
            testValue: $row['testValue'],
            testReference: $row['testReference'] ?? ''
        );
    }
}
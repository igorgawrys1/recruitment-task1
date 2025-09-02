<?php

namespace App\Services;

use App\DTOs\PatientDataDTO;
use App\Repositories\Contracts\PatientRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\TestResultRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CsvImportService
{
    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly TestResultRepositoryInterface $testResultRepository
    ) {}

    public function importFromFile(string $filePath, ?callable $progressCallback = null): array
    {
        $results = [
            'success' => 0,
            'errors' => 0,
            'error_messages' => [],
        ];

        if (!file_exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception("Cannot open file: {$filePath}");
        }

        $headers = $this->parseCsvHeaders($handle);
        
        $totalLines = 0;
        while (fgetcsv($handle, 1000, ';') !== false) {
            $totalLines++;
        }
        rewind($handle);
        $this->parseCsvHeaders($handle);
        
        $lineNumber = 1;
        $processedLines = 0;

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            $lineNumber++;
            $processedLines++;
            
            try {
                $this->processRow($headers, $data);
                $results['success']++;
                Log::info("Successfully imported line {$lineNumber}");
            } catch (Exception $e) {
                $results['errors']++;
                $errorMessage = "Error on line {$lineNumber}: " . $e->getMessage();
                $results['error_messages'][] = $errorMessage;
                Log::error($errorMessage);
            }
            
            if ($progressCallback) {
                $progressCallback($processedLines, $totalLines);
            }
        }

        fclose($handle);
        
        Log::info("Import completed", $results);
        
        return $results;
    }

    private function parseCsvHeaders($handle): array
    {
        $headers = fgetcsv($handle, 1000, ';');
        
        if (!$headers) {
            throw new Exception("Cannot read CSV headers");
        }

        $requiredHeaders = [
            'patientId', 'patientName', 'patientSurname', 'patientSex',
            'patientBirthDate', 'orderId', 'testName', 'testValue', 'testReference'
        ];

        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headers)) {
                throw new Exception("Missing required header: {$required}");
            }
        }

        return $headers;
    }

    private function processRow(array $headers, array $data): void
    {
        if (count($headers) !== count($data)) {
            throw new Exception("Column count mismatch");
        }

        $row = array_combine($headers, $data);
        
        $this->validateRow($row);
        $this->cleanRowData($row);
        
        DB::transaction(function () use ($row) {
            $dto = PatientDataDTO::fromCsvRow($row);
            
            $patient = $this->patientRepository->findOrCreateFromDTO($dto);
            $order = $this->orderRepository->findOrCreateByOrderId($dto->orderId, $patient);
            $this->testResultRepository->createFromDTO($dto, $order);
        });
    }

    private function validateRow(array $row): void
    {
        $requiredFields = ['patientId', 'patientName', 'patientSurname', 'patientSex', 
                          'patientBirthDate', 'orderId', 'testName', 'testValue'];
        
        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                throw new Exception("Missing required field: {$field}");
            }
        }

        if (!in_array(strtolower($row['patientSex']), ['male', 'female', 'm', 'f'])) {
            throw new Exception("Invalid sex value: {$row['patientSex']}");
        }

        if (!strtotime($row['patientBirthDate'])) {
            throw new Exception("Invalid birth date format: {$row['patientBirthDate']}");
        }
    }

    private function cleanRowData(array &$row): void
    {
        if (isset($row['testReference'])) {
            $row['testReference'] = str_replace(['\\X0A\\', '\\X0A', '\X0A'], ' | ', $row['testReference']);
            $row['testReference'] = str_replace('\\', '', $row['testReference']);
            $row['testReference'] = trim($row['testReference']);
        }
    }
}
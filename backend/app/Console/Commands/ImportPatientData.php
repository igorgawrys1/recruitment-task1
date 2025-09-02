<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CsvImportService;
use Exception;

class ImportPatientData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:patients {file=results.csv : Path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import patient data and test results from CSV file';

    public function __construct(
        private readonly CsvImportService $importService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $filePath = base_path($filePath);
            if (!file_exists($filePath)) {
                $this->error("File not found: {$this->argument('file')}");
                return Command::FAILURE;
            }
        }

        $this->info("Starting import from: {$filePath}");
        $this->info("This may take a while...");
        
        try {
            $progressBar = $this->output->createProgressBar();
            
            $results = $this->importService->importFromFile($filePath, function ($current, $total) use ($progressBar) {
                if ($progressBar->getMaxSteps() === 0) {
                    $progressBar->start($total);
                }
                $progressBar->setProgress($current);
            });
            
            $progressBar->finish();
            $this->newLine(2);
            
            $this->info("Import completed!");
            $this->info("Successfully imported: {$results['success']} records");
            
            if ($results['errors'] > 0) {
                $this->warn("Errors encountered: {$results['errors']}");
                
                if ($this->option('verbose')) {
                    foreach ($results['error_messages'] as $error) {
                        $this->error($error);
                    }
                }
            }
            
            return Command::SUCCESS;
            
        } catch (Exception $e) {
            $this->newLine();
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

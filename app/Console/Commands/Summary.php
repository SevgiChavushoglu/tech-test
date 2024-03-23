<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Summary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates summary of services from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = storage_path("app/csv/services.csv");

        if (!file_exists($filePath)) {
            $this->error('File does not exist.');
            return;
        }

        $summary = $this->calculateSummary($filePath);

        $this->displaySummary($summary);
    }

    private function calculateSummary($filePath)
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->error('Error opening file.');
            return [];
        }
        // Skip the first line (headers)
        fgetcsv($handle);
        $summary = [];

        while (($data = fgetcsv($handle)) !== false) {

            $country = strtoupper($data[3]);

            if (!isset($summary[$country])) {
                $summary[$country] = 0;
            }

            $summary[$country] += 1;
        }

        fclose($handle);

        return $summary;
    }

    private function displaySummary($summary)
    {
        $this->info('Summary:');
        foreach ($summary as $product => $totalAmount) {
            $this->line("$product: $totalAmount");
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QueryByCountryCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services {countryCode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns the services that  are available in a specific country';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countryCode = strtoupper($this->argument('countryCode'));

        $file = storage_path("app/csv/services.csv");

        if (!file_exists($file)) {
            $this->error('File does not exist.');
            return;
        }
        $services = $this->getServicesByCountryCode($countryCode, $file);

        $headers = array_shift($services); // Remove and store the headers
        if (empty($services)) {
            $this->info('No services found for the given country code.');
        } else {

            $this->info('Services for country ' . $countryCode . ' (Total:' . count($services) . ')');
            $this->table($headers, $services);
        }
    }

    private function getServicesByCountryCode($countryCode, $file)
    {
        $services = [];

        if (($handle = fopen($file, "r")) !== false) {
            $headers = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== false) {
                $dataCountryCode = strtoupper($data[3]);

                if ($dataCountryCode === $countryCode) {
                    $services[] = $data;
                }
            }
            fclose($handle);
        }
        array_unshift($services, $headers);
        return $services;
    }
}

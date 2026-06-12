<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Medicine;
use Illuminate\Support\Str;

#[Signature('app:import-medicines-csv')]
#[Description('Import medicines from CSV file at workspace root')]
class ImportMedicinesCsv extends Command
{
    public function handle()
    {
        $csvPath = 'd:/Documents/HelloMed/hospital_stock_bangladesh.csv';

        if (!file_exists($csvPath)) {
            $this->error("CSV file not found at: {$csvPath}");
            return Command::FAILURE;
        }

        $this->info("Parsing CSV...");

        $file = fopen($csvPath, 'r');
        $header = fgetcsv($file);
        
        $count = 0;

        while ($row = fgetcsv($file)) {
            // handle empty rows
            if (count($header) !== count($row)) continue;
            
            $data = array_combine($header, $row);

            $name = trim($data['Name']);
            if (empty($name)) continue;

            $requiresPrescription = str_contains($data['Requires prescription'] ?? '', '✅');
            $isActive = str_contains($data['Active'] ?? '', '✅');

            Medicine::updateOrCreate(
                ['name' => $name],
                [
                    'medicine_group' => trim($data['Group'] ?? ''),
                    'description' => trim($data['Description'] ?? ''),
                    'strength' => trim($data['Power'] ?? ''),
                    'power' => trim($data['Power'] ?? ''),
                    'amount' => trim($data['Amount'] ?? ''),
                    'manufacturer' => trim($data['Manufacturer'] ?? ''),
                    'buying_price' => floatval($data['Buying Price'] ?? 0),
                    'price' => floatval($data['Selling Price'] ?? 0),
                    'stock_quantity' => intval($data['Stock quantity'] ?? 0),
                    'requires_prescription' => $requiresPrescription,
                    'is_active' => $isActive,
                ]
            );
            $count++;
        }

        fclose($file);

        $this->info("Successfully imported/updated {$count} medicines.");
        return Command::SUCCESS;
    }
}

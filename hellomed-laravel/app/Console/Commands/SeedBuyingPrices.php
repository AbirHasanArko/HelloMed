<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Medicine;

#[Signature('app:seed-buying-prices')]
#[Description('Seed buying prices for existing medicines based on typical margins')]
class SeedBuyingPrices extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding buying prices...');
        
        $medicines = Medicine::all();
        $count = 0;

        foreach ($medicines as $medicine) {
            if ($medicine->requires_prescription) {
                $margin = 0.20;
            } else {
                $margin = 0.30;
            }

            $buyingPrice = $medicine->price * (1 - $margin);
            
            $medicine->update(['buying_price' => $buyingPrice]);
            $count++;
        }

        $this->info("Successfully updated buying prices for {$count} medicines.");
    }
}

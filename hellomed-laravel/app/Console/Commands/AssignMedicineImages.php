<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\Medicine;

#[Signature('app:assign-medicine-images')]
#[Description('Assign generic medicine images to existing medicines')]
class AssignMedicineImages extends Command
{
    public function handle()
    {
        $this->info("Assigning generic images to medicines...");
        
        $medicines = Medicine::whereNull('image_path')->get();
        $count = 0;
        
        foreach ($medicines as $medicine) {
            $searchStr = strtolower($medicine->name . ' ' . $medicine->medicine_group . ' ' . $medicine->amount . ' ' . $medicine->strength);
            
            $imageName = 'tablet.png'; // default
            
            if (str_contains($searchStr, 'syrup') || str_contains($searchStr, 'suspension') || str_contains($searchStr, 'ml') || str_contains($searchStr, 'liquid')) {
                $imageName = 'syrup.png';
            } elseif (str_contains($searchStr, 'injection') || str_contains($searchStr, 'vial') || str_contains($searchStr, 'ampule') || str_contains($searchStr, 'syringe')) {
                $imageName = 'injection.png';
            } elseif (str_contains($searchStr, 'inhaler') || str_contains($searchStr, 'puff') || str_contains($searchStr, 'spray') || str_contains($searchStr, 'aerosol')) {
                $imageName = 'inhaler.png';
            } elseif (str_contains($searchStr, 'ointment') || str_contains($searchStr, 'cream') || str_contains($searchStr, 'gel') || str_contains($searchStr, 'tube')) {
                $imageName = 'ointment.png';
            } elseif (str_contains($searchStr, 'capsule') || str_contains($searchStr, 'cap')) {
                $imageName = 'capsule.png';
            }
            
            $medicine->update(['image_path' => 'medicines/' . $imageName]);
            $count++;
        }
        
        $this->info("Successfully assigned images to {$count} medicines.");
        return Command::SUCCESS;
    }
}

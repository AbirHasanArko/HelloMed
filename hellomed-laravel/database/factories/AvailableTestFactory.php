<?php

namespace Database\Factories;

use App\Models\AvailableTest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailableTest>
 */
class AvailableTestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tests = [
            'Complete Blood Count (CBC)', 'Lipid Profile', 'Liver Function Test (LFT)',
            'Kidney Function Test (KFT)', 'Thyroid Profile', 'Blood Sugar Fasting',
            'HbA1c', 'Urine Routine', 'Vitamin D', 'Vitamin B12',
            'Electrocardiogram (ECG)', 'Chest X-Ray', 'Ultrasound Whole Abdomen',
            'MRI Brain', 'CT Scan Thorax'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($tests),
            'description' => $this->faker->paragraph(),
            'lab_room_number' => $this->faker->numberBetween(100, 999),
            'location' => $this->faker->randomElement(['Main Building, Floor 1', 'Main Building, Floor 2', 'Annex Building, Ground Floor']),
            'fee_bdt' => $this->faker->randomFloat(2, 500, 15000),
            'is_active' => true,
        ];
    }
}

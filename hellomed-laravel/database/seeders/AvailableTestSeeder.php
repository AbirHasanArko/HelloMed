<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AvailableTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tests = [
            // 1. Basic Blood Tests
            ['name' => 'CBC', 'fee_bdt' => 600, 'category' => 'Basic Blood Tests'],
            ['name' => 'Blood Sugar Fasting (FBS)', 'fee_bdt' => 180, 'category' => 'Basic Blood Tests'],
            ['name' => 'Blood Sugar Random (RBS)', 'fee_bdt' => 180, 'category' => 'Basic Blood Tests'],
            ['name' => 'Post Prandial Blood Sugar (PPBS)', 'fee_bdt' => 200, 'category' => 'Basic Blood Tests'],
            ['name' => 'ESR', 'fee_bdt' => 250, 'category' => 'Basic Blood Tests'],
            ['name' => 'Hemoglobin', 'fee_bdt' => 220, 'category' => 'Basic Blood Tests'],
            ['name' => 'Blood Group & Rh', 'fee_bdt' => 250, 'category' => 'Basic Blood Tests'],
            ['name' => 'Platelet Count', 'fee_bdt' => 400, 'category' => 'Basic Blood Tests'],
            ['name' => 'BT (Bleeding Time)', 'fee_bdt' => 200, 'category' => 'Basic Blood Tests'],
            ['name' => 'CT (Clotting Time)', 'fee_bdt' => 200, 'category' => 'Basic Blood Tests'],
            ['name' => 'PBF', 'fee_bdt' => 600, 'category' => 'Basic Blood Tests'],
            ['name' => 'PT/INR', 'fee_bdt' => 1200, 'category' => 'Basic Blood Tests'],

            // 2. Liver Function Tests (LFT Panel)
            ['name' => 'LFT (Full Panel)', 'fee_bdt' => 1500, 'category' => 'Liver Function'],
            ['name' => 'ALT (SGPT)', 'fee_bdt' => 450, 'category' => 'Liver Function'],
            ['name' => 'AST (SGOT)', 'fee_bdt' => 450, 'category' => 'Liver Function'],
            ['name' => 'ALP', 'fee_bdt' => 500, 'category' => 'Liver Function'],
            ['name' => 'Bilirubin Total', 'fee_bdt' => 350, 'category' => 'Liver Function'],
            ['name' => 'Bilirubin Direct', 'fee_bdt' => 350, 'category' => 'Liver Function'],
            ['name' => 'Total Protein', 'fee_bdt' => 450, 'category' => 'Liver Function'],
            ['name' => 'Albumin', 'fee_bdt' => 450, 'category' => 'Liver Function'],

            // 3. Kidney Function Tests (KFT Panel)
            ['name' => 'KFT (Full Panel)', 'fee_bdt' => 1500, 'category' => 'Kidney Function'],
            ['name' => 'Creatinine', 'fee_bdt' => 300, 'category' => 'Kidney Function'],
            ['name' => 'Urea', 'fee_bdt' => 350, 'category' => 'Kidney Function'],
            ['name' => 'Uric Acid', 'fee_bdt' => 450, 'category' => 'Kidney Function'],
            ['name' => 'Electrolytes', 'fee_bdt' => 1200, 'category' => 'Kidney Function'],
            ['name' => 'Calcium', 'fee_bdt' => 600, 'category' => 'Kidney Function'],
            ['name' => 'Phosphorus', 'fee_bdt' => 600, 'category' => 'Kidney Function'],
            ['name' => 'Magnesium', 'fee_bdt' => 800, 'category' => 'Kidney Function'],

            // 4. Diabetes Tests
            ['name' => 'HbA1c', 'fee_bdt' => 1200, 'category' => 'Diabetes'],
            ['name' => 'OGTT', 'fee_bdt' => 1500, 'category' => 'Diabetes'],
            ['name' => 'Insulin Level', 'fee_bdt' => 1800, 'category' => 'Diabetes'],
            ['name' => 'C-Peptide', 'fee_bdt' => 2000, 'category' => 'Diabetes'],

            // 5. Thyroid & Hormones
            ['name' => 'Thyroid Profile', 'fee_bdt' => 1500, 'category' => 'Hormones'],
            ['name' => 'TSH', 'fee_bdt' => 700, 'category' => 'Hormones'],
            ['name' => 'T3', 'fee_bdt' => 600, 'category' => 'Hormones'],
            ['name' => 'T4', 'fee_bdt' => 600, 'category' => 'Hormones'],
            ['name' => 'Prolactin', 'fee_bdt' => 1500, 'category' => 'Hormones'],
            ['name' => 'Testosterone', 'fee_bdt' => 2000, 'category' => 'Hormones'],
            ['name' => 'Estrogen', 'fee_bdt' => 2000, 'category' => 'Hormones'],
            ['name' => 'Progesterone', 'fee_bdt' => 2000, 'category' => 'Hormones'],
            ['name' => 'FSH', 'fee_bdt' => 1500, 'category' => 'Hormones'],
            ['name' => 'LH', 'fee_bdt' => 1500, 'category' => 'Hormones'],

            // 6. Infection / Immunology
            ['name' => 'CRP', 'fee_bdt' => 800, 'category' => 'Immunology'],
            ['name' => 'ASO Titre', 'fee_bdt' => 700, 'category' => 'Immunology'],
            ['name' => 'RA Factor', 'fee_bdt' => 800, 'category' => 'Immunology'],
            ['name' => 'Dengue NS1', 'fee_bdt' => 600, 'category' => 'Immunology'],
            ['name' => 'Dengue IgG/IgM', 'fee_bdt' => 800, 'category' => 'Immunology'],
            ['name' => 'HBsAg', 'fee_bdt' => 700, 'category' => 'Immunology'],
            ['name' => 'Anti-HCV', 'fee_bdt' => 1500, 'category' => 'Immunology'],
            ['name' => 'HIV Test', 'fee_bdt' => 1200, 'category' => 'Immunology'],
            ['name' => 'Widal Test', 'fee_bdt' => 400, 'category' => 'Immunology'],
            ['name' => 'Mantoux Test', 'fee_bdt' => 500, 'category' => 'Immunology'],

            // 7. Urine & Stool
            ['name' => 'Urine Routine', 'fee_bdt' => 200, 'category' => 'Urine & Stool'],
            ['name' => 'Urine Culture', 'fee_bdt' => 1000, 'category' => 'Urine & Stool'],
            ['name' => 'Urine Pregnancy Test', 'fee_bdt' => 200, 'category' => 'Urine & Stool'],
            ['name' => 'Stool Routine', 'fee_bdt' => 250, 'category' => 'Urine & Stool'],
            ['name' => 'Stool Occult Blood', 'fee_bdt' => 500, 'category' => 'Urine & Stool'],

            // 8. Allergy / Skin / Dermatology
            ['name' => 'Total IgE', 'fee_bdt' => 2500, 'category' => 'Dermatology'],
            ['name' => 'Skin Patch Test', 'fee_bdt' => 4000, 'category' => 'Dermatology'],
            ['name' => 'KOH Test', 'fee_bdt' => 500, 'category' => 'Dermatology'],
            ['name' => 'Skin Biopsy', 'fee_bdt' => 3500, 'category' => 'Dermatology'],
            ['name' => 'Allergy Panel', 'fee_bdt' => 7000, 'category' => 'Dermatology'],

            // 9. Imaging / Radiology
            ['name' => 'Chest X-Ray', 'fee_bdt' => 600, 'category' => 'Radiology'],
            ['name' => 'X-Ray Other Parts', 'fee_bdt' => 500, 'category' => 'Radiology'],
            ['name' => 'Ultrasound Whole Abdomen', 'fee_bdt' => 1500, 'category' => 'Radiology'],
            ['name' => 'Ultrasound Pelvis', 'fee_bdt' => 1200, 'category' => 'Radiology'],
            ['name' => 'Pregnancy USG', 'fee_bdt' => 1000, 'category' => 'Radiology'],
            ['name' => 'CT Brain', 'fee_bdt' => 6000, 'category' => 'Radiology'],
            ['name' => 'CT Thorax', 'fee_bdt' => 7000, 'category' => 'Radiology'],
            ['name' => 'CT Abdomen', 'fee_bdt' => 9000, 'category' => 'Radiology'],
            ['name' => 'MRI Brain', 'fee_bdt' => 12000, 'category' => 'Radiology'],
            ['name' => 'MRI Spine', 'fee_bdt' => 13000, 'category' => 'Radiology'],

            // 10. Cardiac Tests
            ['name' => 'ECG', 'fee_bdt' => 400, 'category' => 'Cardiac'],
            ['name' => '2D Echo', 'fee_bdt' => 3500, 'category' => 'Cardiac'],
            ['name' => 'TMT', 'fee_bdt' => 4000, 'category' => 'Cardiac'],
            ['name' => 'Troponin I', 'fee_bdt' => 1500, 'category' => 'Cardiac'],
            ['name' => 'CK-MB', 'fee_bdt' => 800, 'category' => 'Cardiac'],

            // 11. Vitamins & Nutrition
            ['name' => 'Vitamin D', 'fee_bdt' => 3500, 'category' => 'Nutrition'],
            ['name' => 'Vitamin B12', 'fee_bdt' => 2000, 'category' => 'Nutrition'],
            ['name' => 'Ferritin', 'fee_bdt' => 1500, 'category' => 'Nutrition'],
            ['name' => 'Iron Profile', 'fee_bdt' => 2000, 'category' => 'Nutrition'],
            ['name' => 'Folate', 'fee_bdt' => 1800, 'category' => 'Nutrition'],

            // 12. Gastro / Endoscopy
            ['name' => 'Endoscopy', 'fee_bdt' => 2500, 'category' => 'Gastroenterology'],
            ['name' => 'Colonoscopy', 'fee_bdt' => 6000, 'category' => 'Gastroenterology'],
            ['name' => 'H. Pylori Test', 'fee_bdt' => 1200, 'category' => 'Gastroenterology'],
            ['name' => 'Stool Calprotectin', 'fee_bdt' => 5000, 'category' => 'Gastroenterology'],

            // 13. Special / Advanced
            ['name' => 'DEXA Bone Scan', 'fee_bdt' => 5000, 'category' => 'Advanced'],
            ['name' => 'Pap Smear', 'fee_bdt' => 1000, 'category' => 'Advanced'],
            ['name' => 'Sputum AFB', 'fee_bdt' => 400, 'category' => 'Advanced'],
            ['name' => 'GeneXpert TB', 'fee_bdt' => 3000, 'category' => 'Advanced'],
            ['name' => 'PSA', 'fee_bdt' => 2000, 'category' => 'Advanced'],
            ['name' => 'CA-125', 'fee_bdt' => 3000, 'category' => 'Advanced'],
            ['name' => 'CA-19.9', 'fee_bdt' => 3000, 'category' => 'Advanced'],
            ['name' => 'CEA', 'fee_bdt' => 3000, 'category' => 'Advanced'],
        ];

        // Delete all previously seeded tests and reset auto-increment to 1 to clean up
        \Illuminate\Support\Facades\DB::table('available_tests')->truncate();

        $insertData = [];
        foreach ($tests as $test) {
            $insertData[] = [
                'name' => $test['name'],
                'slug' => Str::slug($test['name']),
                'description' => "Standard " . strtolower($test['category']) . " test (" . $test['name'] . "). Contact lab desk for further instructions.",
                'lab_room_number' => rand(101, 199),
                'location' => 'Main Lab Wing, Floor ' . rand(1, 3),
                'fee_bdt' => $test['fee_bdt'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\AvailableTest::insert($insertData);
    }
}

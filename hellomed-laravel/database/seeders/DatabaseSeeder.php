<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Medicine;
use App\Models\User;
use App\Models\Appointment;
use App\Models\AppointmentPrescriptionItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@hellomed.test',
        ], [
            'name' => 'Platform Admin',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::query()->updateOrCreate([
            'email' => 'staff@hellomed.test',
        ], [
            'name' => 'Hospital Staff',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::query()->updateOrCreate([
            'email' => 'pharmacist@hellomed.test',
        ], [
            'name' => 'Hospital Pharmacist',
            'password' => Hash::make('password123'),
            'role' => 'pharmacist',
            'is_active' => true,
        ]);

        $doctorUser1 = User::query()->updateOrCreate([
            'email' => 'doctor@hellomed.test',
        ], [
            'name' => 'Doctor Account',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $doctorUser2 = User::query()->firstOrCreate(['email' => 'tariq@hellomed.test'], [
            'name' => 'Dr. Tariq Rahman',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser3 = User::query()->firstOrCreate(['email' => 'imran@hellomed.test'], [
            'name' => 'Dr. Imran Ahmed',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser4 = User::query()->firstOrCreate(['email' => 'kamal@hellomed.test'], [
            'name' => 'Dr. Kamal Uddin',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser5 = User::query()->firstOrCreate(['email' => 'rashedul@hellomed.test'], [
            'name' => 'Dr. Rashedul Islam',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser6 = User::query()->firstOrCreate(['email' => 'shafiqur@hellomed.test'], [
            'name' => 'Dr. Shafiqur Rahman',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser7 = User::query()->firstOrCreate(['email' => 'arafat@hellomed.test'], [
            'name' => 'Dr. Arafat Hossain',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);
        $doctorUser8 = User::query()->firstOrCreate(['email' => 'nazmul@hellomed.test'], [
            'name' => 'Dr. Nazmul Huda',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patientUser = User::query()->updateOrCreate([
            'email' => 'patient@hellomed.test',
        ], [
            'name' => 'Demo Patient',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);
        
        $otherPatientUser = User::query()->updateOrCreate([
            'email' => 'patient2@hellomed.test',
        ], [
            'name' => 'Sarah Connor',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $departments = collect([
            ['name' => 'Cardiology', 'description' => 'Heart care and cardiovascular treatment.', 'service_scope' => 'online and offline', 'image_path' => 'departments/cardiology.png'],
            ['name' => 'Orthopedics', 'description' => 'Bone, joint, muscle, and mobility care.', 'service_scope' => 'offline and online booking', 'image_path' => 'departments/orthopedics.png'],
            ['name' => 'Dental', 'description' => 'Oral health, surgery, and preventive treatment.', 'service_scope' => 'offline and online booking', 'image_path' => 'departments/dental.png'],
            ['name' => 'Psychiatry', 'description' => 'Mental health evaluation and therapy support.', 'service_scope' => 'online and offline', 'image_path' => 'departments/psychiatry.png'],
            ['name' => 'Neurology', 'description' => 'Brain, spinal cord, and nervous system disorders.', 'service_scope' => 'online and offline', 'image_path' => 'departments/neurology.png'],
            ['name' => 'Pediatrics', 'description' => 'Specialized medical care for infants, children, and adolescents.', 'service_scope' => 'offline and online booking', 'image_path' => 'departments/pediatrics.png'],
            ['name' => 'Dermatology', 'description' => 'Skin, hair, and nail health and treatments.', 'service_scope' => 'online and offline', 'image_path' => 'departments/dermatology.png'],
            ['name' => 'Oncology', 'description' => 'Comprehensive cancer care and chemotherapy.', 'service_scope' => 'offline booking', 'image_path' => 'departments/oncology.png'],
        ]);

        $departments->each(function (array $item): void {
            Department::query()->updateOrCreate([
                'slug' => \Illuminate\Support\Str::slug($item['name']),
            ], $item + ['is_active' => true]);
        });

        $catGenHealth = ArticleCategory::query()->updateOrCreate([
            'slug' => 'general-health',
        ], [
            'name' => 'General Health',
            'description' => 'Hospital guidance, health education, and department updates.',
            'is_active' => true,
        ]);
        
        $catChildCare = ArticleCategory::query()->updateOrCreate([
            'slug' => 'child-care',
        ], [
            'name' => 'Child Care',
            'description' => 'Pediatrics and children health.',
            'is_active' => true,
        ]);
        
        $catMentalHealth = ArticleCategory::query()->updateOrCreate([
            'slug' => 'mental-health',
        ], [
            'name' => 'Mental Health',
            'description' => 'Psychological well-being and therapy.',
            'is_active' => true,
        ]);

        $cardiology = Department::query()->where('slug', 'cardiology')->first();
        $orthopedics = Department::query()->where('slug', 'orthopedics')->first();
        $neurology = Department::query()->where('slug', 'neurology')->first();
        $pediatrics = Department::query()->where('slug', 'pediatrics')->first();
        $dermatology = Department::query()->where('slug', 'dermatology')->first();

        $drMahmud = null;

        if ($cardiology) {
            $drMahmud = Doctor::query()->updateOrCreate([
                'slug' => 'dr-mahmud-hasan',
            ], [
                'department_id' => $cardiology->id,
                'user_id' => $doctorUser1->id,
                'name' => 'Dr. Mahmud Hasan',
                'specialty' => 'Interventional Cardiology',
                'bio' => 'Cardiology consultant with experience in outpatient and inpatient care.',
                'qualification' => 'MBBS, FCPS (Cardiology)',
                'experience_years' => 12,
                'consultation_fee' => 1200,
                'online_fee' => 1000,
                'offline_fee' => 1200,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Level 4, Cardiac Wing, HelloMed Hospital',
                'photo_path' => 'doctors/mahmud.png',
                'available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'saturday'],
                'available_from' => '09:00:00',
                'available_to' => '17:00:00',
                'online_available_days' => ['monday', 'tuesday', 'wednesday', 'thursday'],
                'online_available_from' => '09:00:00',
                'online_available_to' => '12:00:00',
                'offline_available_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'saturday'],
                'offline_available_from' => '14:00:00',
                'offline_available_to' => '17:00:00',
                'slot_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        if ($orthopedics) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-rashedul-islam',
            ], [
                'department_id' => $orthopedics->id,
                'user_id' => $doctorUser5->id,
                'name' => 'Dr. Rashedul Islam',
                'specialty' => 'Orthopedic Surgery',
                'bio' => 'Handles fractures, joint care, and mobility treatment plans.',
                'qualification' => 'MBBS, MS (Orthopedics)',
                'experience_years' => 10,
                'consultation_fee' => 1000,
                'online_fee' => 900,
                'offline_fee' => 1000,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Orthopedic OPD, HelloMed Hospital',
                'photo_path' => 'doctors/rashedul.png',
                'available_days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
                'available_from' => '10:00:00',
                'available_to' => '18:00:00',
                'slot_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        $oncology = Department::where('name', 'Oncology')->first();
        if ($oncology) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-kamal-uddin',
            ], [
                'department_id' => $oncology->id,
                'user_id' => $doctorUser4->id,
                'name' => 'Dr. Kamal Uddin',
                'specialty' => 'Oncologist',
                'bio' => 'Comprehensive cancer care and chemotherapy treatments.',
                'qualification' => 'MBBS, FCPS (Oncology)',
                'experience_years' => 12,
                'consultation_fee' => 1200,
                'online_fee' => 1000,
                'offline_fee' => 1200,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Oncology Center, HelloMed Hospital',
                'photo_path' => 'doctors/kamal.png',
                'available_days' => ['sunday', 'monday', 'wednesday', 'thursday', 'saturday'],
                'available_from' => '09:00:00',
                'available_to' => '15:00:00',
                'slot_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        $dental = Department::where('name', 'Dental')->first();
        if ($dental) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-shafiqur-rahman',
            ], [
                'department_id' => $dental->id,
                'user_id' => $doctorUser6->id,
                'name' => 'Dr. Shafiqur Rahman',
                'specialty' => 'Dental Care',
                'bio' => 'Specialized in dental surgery and oral health.',
                'qualification' => 'BDS, FCPS',
                'experience_years' => 14,
                'consultation_fee' => 800,
                'online_fee' => 700,
                'offline_fee' => 800,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Dental Wing, HelloMed Hospital',
                'photo_path' => 'doctors/shafiqur.png',
                'available_days' => ['sunday', 'monday', 'wednesday', 'thursday'],
                'available_from' => '09:00:00',
                'available_to' => '15:00:00',
                'slot_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        $dermatology = Department::where('name', 'Dermatology')->first();
        if ($dermatology) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-arafat-hossain',
            ], [
                'department_id' => $dermatology->id,
                'user_id' => $doctorUser7->id,
                'name' => 'Dr. Arafat Hossain',
                'specialty' => 'Dermatologist',
                'bio' => 'Expert in skin care, laser treatments, and cosmetic dermatology.',
                'qualification' => 'MBBS, DDV',
                'experience_years' => 7,
                'consultation_fee' => 900,
                'online_fee' => 800,
                'offline_fee' => 900,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Skin Clinic, HelloMed Hospital',
                'photo_path' => 'doctors/arafat.png',
                'available_days' => ['saturday', 'sunday', 'monday', 'wednesday'],
                'available_from' => '10:00:00',
                'available_to' => '18:00:00',
                'slot_minutes' => 20,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        $psychiatry = Department::where('name', 'Psychiatry')->first();
        if ($psychiatry) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-nazmul-huda',
            ], [
                'department_id' => $psychiatry->id,
                'user_id' => $doctorUser8->id,
                'name' => 'Dr. Nazmul Huda',
                'specialty' => 'Psychiatrist',
                'bio' => 'Mental health advocate focusing on cognitive behavioral therapies.',
                'qualification' => 'MBBS, MD (Psychiatry)',
                'experience_years' => 11,
                'consultation_fee' => 1500,
                'online_fee' => 1200,
                'offline_fee' => 1500,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Wellness Wing, HelloMed Hospital',
                'photo_path' => 'doctors/nazmul.png',
                'available_days' => ['sunday', 'tuesday', 'thursday'],
                'available_from' => '14:00:00',
                'available_to' => '20:00:00',
                'slot_minutes' => 45,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        if ($neurology) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-tariq-rahman',
            ], [
                'department_id' => $neurology->id,
                'user_id' => $doctorUser2->id,
                'name' => 'Dr. Tariq Rahman',
                'specialty' => 'Neurology Specialist',
                'bio' => 'Expert in treating neurological disorders including migraines, epilepsy, and strokes.',
                'qualification' => 'MBBS, MD (Neurology)',
                'experience_years' => 15,
                'consultation_fee' => 1500,
                'online_fee' => 1200,
                'offline_fee' => 1500,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Level 5, Neuro Wing, HelloMed Hospital',
                'photo_path' => 'doctors/tariq.png',
                'available_days' => ['sunday', 'monday', 'wednesday', 'thursday'],
                'available_from' => '10:00:00',
                'available_to' => '16:00:00',
                'slot_minutes' => 30,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        if ($pediatrics) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-imran-ahmed',
            ], [
                'department_id' => $pediatrics->id,
                'user_id' => $doctorUser3->id,
                'name' => 'Dr. Imran Ahmed',
                'specialty' => 'Pediatrics',
                'bio' => 'Dedicated to child healthcare, vaccinations, and pediatric development.',
                'qualification' => 'MBBS, DCH, FCPS',
                'experience_years' => 8,
                'consultation_fee' => 800,
                'online_fee' => 600,
                'offline_fee' => 800,
                'online_available' => true,
                'offline_available' => true,
                'clinic_address' => 'Level 2, Child Care Center, HelloMed Hospital',
                'photo_path' => 'doctors/imran.png',
                'available_days' => ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday'],
                'available_from' => '11:00:00',
                'available_to' => '19:00:00',
                'slot_minutes' => 20,
                'is_featured' => true,
                'is_active' => true,
            ]);
        }

        if ($dermatology) {
            Doctor::query()->updateOrCreate([
                'slug' => 'dr-kamal-hossain',
            ], [
                'department_id' => $dermatology->id,
                'name' => 'Dr. Kamal Hossain',
                'specialty' => 'Dermatologist & Cosmetologist',
                'bio' => 'Treats all kinds of skin, hair, and nail problems.',
                'qualification' => 'MBBS, DDV',
                'experience_years' => 20,
                'consultation_fee' => 1000,
                'online_fee' => 800,
                'offline_fee' => 1000,
                'online_available' => true,
                'offline_available' => false,
                'clinic_address' => 'Level 3, Skin Center, HelloMed Hospital',
                'photo_path' => 'doctors/kamal.png',
                'available_days' => ['monday', 'tuesday', 'wednesday'],
                'available_from' => '16:00:00',
                'available_to' => '20:00:00',
                'slot_minutes' => 15,
                'is_featured' => false,
                'is_active' => true,
            ]);
        }

        Article::query()->updateOrCreate([
            'slug' => 'when-to-see-a-cardiologist',
        ], [
            'article_category_id' => $catGenHealth->id,
            'title' => 'When to See a Cardiologist',
            'excerpt' => 'Learn the warning signs that mean you should book a cardiology appointment early.',
            'body' => "<p>Chest pain, breathlessness, and abnormal blood pressure are common reasons to seek a cardiology evaluation. Early diagnosis improves outcomes.</p><h2>The Importance of Early Detection</h2><p>Cardiovascular diseases are the leading cause of death globally, but many of these conditions are highly preventable and treatable when caught early. If you experience any of the following, it is crucial to consult a specialist:</p><ul><li>Persistent shortness of breath</li><li>Unexplained dizziness or lightheadedness</li><li>A fluttering sensation in your chest (palpitations)</li></ul><h2>Know Your Risk Factors</h2><p>Beyond symptoms, a family history of heart disease is a significant risk factor. Even if you feel perfectly healthy, scheduling a routine check-up after the age of 40 can help establish a baseline for your heart health.</p><blockquote>\"Don't wait for an emergency. Preventive cardiology focuses on lifestyle modifications, including diet and exercise counseling, alongside medication management to keep your heart functioning at its best.\"</blockquote><p>Your cardiologist may recommend an electrocardiogram (ECG), a stress test, or blood work to monitor your cholesterol and blood sugar levels.</p>",
            'cover_image_path' => 'article-covers/cardiologist.png',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
        ]);
        
        Article::query()->updateOrCreate([
            'slug' => 'understanding-pediatric-care',
        ], [
            'article_category_id' => $catChildCare->id,
            'user_id' => $doctorUser3->id,
            'title' => 'Understanding Pediatric Care Milestones',
            'excerpt' => 'A guide for new parents on what to look out for in the first year of your baby\'s life.',
            'body' => "<p>Your child's development is critical during the first 12 months. Routine checks are not just for vaccines, but for monitoring motor skills, language development, and nutritional health.</p><h2>The First Six Months</h2><p>During the first few months, doctors will focus heavily on tracking your baby's height, weight, and head circumference. They will also look for early social milestones, such as:</p><ul><li>Smiling in response to your voice</li><li>Following objects with their eyes</li><li>Lifting their head during tummy time</li></ul><p>By six months, most babies are rolling over, babbling, and reaching for toys. This is also a critical time to discuss introducing solid foods and establishing healthy sleep habits.</p><h2>Approaching the First Birthday</h2><p>As your baby approaches their first birthday, milestones like pulling up to stand, saying their first words, and playing simple games (like peek-a-boo) become important markers of cognitive and physical development.</p><blockquote>\"Remember that every child develops at their own pace. The goal of pediatric visits is to provide support, answer your questions, and ensure your child is thriving.\"</blockquote>",
            'cover_image_path' => 'articles/pediatrics.png',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
        ]);
        
        Article::query()->updateOrCreate([
            'slug' => 'managing-mental-health',
        ], [
            'article_category_id' => $catMentalHealth->id,
            'title' => 'Managing Mental Health in the Digital Age',
            'excerpt' => 'Tips and tricks to stay grounded and manage stress in a hyper-connected world.',
            'body' => "<p>Digital detoxing is no longer just a trend, it is a medical necessity. Constant notifications cause our cortisol levels to spike. Taking 30 minutes of disconnected time daily can drastically improve your mental wellbeing.</p><h2>The Rise of Technostress</h2><p>The modern workplace often demands constant connectivity, leading to a phenomenon known as <strong>'technostress'</strong>. This can manifest as:</p><ul><li>Anxiety and irritability</li><li>Disrupted sleep patterns</li><li>A general feeling of overwhelm and burnout</li></ul><p>Setting boundaries is the first step. Try implementing a <em>'no-screens'</em> rule one hour before bedtime to allow your brain to naturally produce melatonin.</p><h2>Mindfulness in Practice</h2><p>Additionally, incorporating mindfulness practices into your daily routine can help combat digital fatigue. Simple breathing exercises, short walks without your phone, or engaging in a physical hobby can anchor you back to the present moment.</p><blockquote>\"If feelings of anxiety or depression persist, do not hesitate to reach out to a mental health professional for personalized strategies.\"</blockquote>",
            'cover_image_path' => 'article-covers/mental_health.png',
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $medicines = [
            [
                'name' => 'Napa', 'medicine_group' => 'Paracetamol', 'power' => '500mg', 'amount' => '1 strip x 10 tablets', 'strength' => '500mg',
                'manufacturer' => 'Beximco Pharma', 'description' => 'Pain and fever relief tablet.', 'price' => 3.50, 'stock_quantity' => 500, 'requires_prescription' => false, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Omeprazole', 'medicine_group' => 'Omeprazole', 'power' => '20mg', 'amount' => '1 strip x 10 capsules', 'strength' => '20mg',
                'manufacturer' => 'Square Pharma', 'description' => 'Acid reflux and gastric control capsule.', 'price' => 8.00, 'stock_quantity' => 300, 'requires_prescription' => false, 'is_active' => true,
                'image_path' => 'medicines/capsules.png'
            ],
            [
                'name' => 'Cefixime', 'medicine_group' => 'Cefixime', 'power' => '200mg', 'amount' => '1 strip x 10 tablets', 'strength' => '200mg',
                'manufacturer' => 'Incepta', 'description' => 'Antibiotic capsule. Use on physician advice.', 'price' => 38.00, 'stock_quantity' => 120, 'requires_prescription' => true, 'is_active' => true,
                'image_path' => 'medicines/capsules.png'
            ],
            [
                'name' => 'Azithromycin', 'medicine_group' => 'Azithromycin', 'power' => '500mg', 'amount' => '1 strip x 6 tablets', 'strength' => '500mg',
                'manufacturer' => 'Beximco Pharma', 'description' => 'Macrolide antibiotic used for respiratory infections.', 'price' => 45.00, 'stock_quantity' => 150, 'requires_prescription' => true, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Vitamin C', 'medicine_group' => 'Ascorbic Acid', 'power' => '500mg', 'amount' => '1 bottle x 30 tablets', 'strength' => '500mg',
                'manufacturer' => 'Square Pharma', 'description' => 'Chewable vitamin C for immune support.', 'price' => 120.00, 'stock_quantity' => 800, 'requires_prescription' => false, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Amoxicillin Syrup', 'medicine_group' => 'Amoxicillin', 'power' => '125mg/5ml', 'amount' => '1 bottle x 100ml', 'strength' => '125mg/5ml',
                'manufacturer' => 'Incepta', 'description' => 'Liquid antibiotic for children.', 'price' => 65.00, 'stock_quantity' => 200, 'requires_prescription' => true, 'is_active' => true,
                'image_path' => 'medicines/syrup.png'
            ],
            [
                'name' => 'Ibuprofen', 'medicine_group' => 'Ibuprofen', 'power' => '400mg', 'amount' => '1 strip x 10 tablets', 'strength' => '400mg',
                'manufacturer' => 'Renata Ltd', 'description' => 'Anti-inflammatory painkiller.', 'price' => 12.50, 'stock_quantity' => 400, 'requires_prescription' => false, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Cetirizine', 'medicine_group' => 'Cetirizine Hydrochloride', 'power' => '10mg', 'amount' => '1 strip x 10 tablets', 'strength' => '10mg',
                'manufacturer' => 'Square Pharma', 'description' => 'Antihistamine for allergy relief.', 'price' => 5.00, 'stock_quantity' => 600, 'requires_prescription' => false, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Losartan Potassium', 'medicine_group' => 'Losartan', 'power' => '50mg', 'amount' => '1 strip x 10 tablets', 'strength' => '50mg',
                'manufacturer' => 'Beximco Pharma', 'description' => 'Used to treat high blood pressure.', 'price' => 18.00, 'stock_quantity' => 250, 'requires_prescription' => true, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
            [
                'name' => 'Metformin', 'medicine_group' => 'Metformin Hydrochloride', 'power' => '500mg', 'amount' => '1 strip x 10 tablets', 'strength' => '500mg',
                'manufacturer' => 'Incepta', 'description' => 'Type 2 diabetes medication.', 'price' => 10.00, 'stock_quantity' => 300, 'requires_prescription' => true, 'is_active' => true,
                'image_path' => 'medicines/tablets.png'
            ],
        ];

        foreach ($medicines as $medicine) {
            Medicine::query()->updateOrCreate([
                'slug' => \Illuminate\Support\Str::slug($medicine['name'].'-'.$medicine['strength']),
            ], $medicine);
        }

        $napa = Medicine::where('name', 'Napa')->first();
        $omeprazole = Medicine::where('name', 'Omeprazole')->first();

        // Create Demo Appointment and Prescription for Patient
        if ($drMahmud) {
            $appointment = Appointment::query()->updateOrCreate([
                'patient_email' => 'patient@hellomed.test',
            ], [
                'user_id' => $patientUser->id,
                'doctor_id' => $drMahmud->id,
                'department_id' => $drMahmud->department_id,
                'patient_name' => 'Demo Patient',
                'patient_phone' => '01711000000',
                'service_mode' => 'online',
                'scheduled_for' => Carbon::yesterday()->setHour(10)->setMinute(0),
                'reason' => 'Mild chest pain and acid reflux.',
                'status' => 'completed',
                'payment_method' => 'bkash',
                'payment_status' => 'paid',
                'prescription_written_at' => Carbon::yesterday()->setHour(10)->setMinute(20),
                'prescription_diagnosis' => 'GERD (Gastroesophageal Reflux Disease)',
                'prescription_advice' => 'Avoid spicy food. Drink plenty of water. Elevate head while sleeping.',
                'prescription_safety_notes' => 'If chest pain radiates to the arm, visit emergency immediately.',
                'prescription_follow_up_date' => Carbon::today()->addDays(14),
            ]);

            if ($appointment->wasRecentlyCreated || $appointment->prescriptionItems()->count() === 0) {
                if ($napa) {
                    AppointmentPrescriptionItem::create([
                        'appointment_id' => $appointment->id,
                        'medicine_id' => $napa->id,
                        'medicine_name' => $napa->name,
                        'dosage' => '1 + 0 + 1 (After meal)',
                        'intake_time' => 'Morning and Night',
                        'amount' => '6 Tablets',
                        'sort_order' => 1,
                    ]);
                }

                if ($omeprazole) {
                    AppointmentPrescriptionItem::create([
                        'appointment_id' => $appointment->id,
                        'medicine_id' => $omeprazole->id,
                        'medicine_name' => $omeprazole->name,
                        'dosage' => '1 + 0 + 0 (Before meal)',
                        'intake_time' => 'Morning',
                        'amount' => '14 Capsules',
                        'sort_order' => 2,
                    ]);
                }
            }
        }
        
        // Add QnA
        $q1 = DB::table('qna_questions')->insertGetId([
            'user_id' => $patientUser->id,
            'title' => 'When should I visit a cardiologist?',
            'question' => 'I have been experiencing slight chest pain after running. Is this normal or should I seek a specialist?',
            'status' => 'answered',
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
        ]);
        
        DB::table('qna_answers')->insert([
            'qna_question_id' => $q1,
            'user_id' => $doctorUser1->id, // Dr Mahmud
            'answer' => 'Chest pain during exertion should never be ignored. It is strongly recommended that you book a consultation so we can run an ECG and properly evaluate your cardiovascular health.',
            'is_official' => true,
            'created_at' => Carbon::now()->subDays(1),
            'updated_at' => Carbon::now()->subDays(1),
        ]);
        
        $q2 = DB::table('qna_questions')->insertGetId([
            'user_id' => $otherPatientUser->id,
            'title' => 'Side effects of Omeprazole',
            'question' => 'Are there long term side effects of taking Omeprazole daily?',
            'status' => 'open',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

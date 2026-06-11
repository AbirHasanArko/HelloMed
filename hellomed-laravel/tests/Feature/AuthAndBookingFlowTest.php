<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\Medicine;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthAndBookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $this->post('/register', [
            'name' => 'Test Patient',
            'email' => 'patient@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'patient@example.com',
            'role' => 'patient',
        ]);

        auth()->logout();

        $this->post('/login', [
            'email' => 'patient@example.com',
            'password' => 'password123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login_success',
            'entity_type' => 'User',
        ]);
    }

    public function test_failed_login_creates_audit_log(): void
    {
        User::query()->create([
            'name' => 'Login User',
            'email' => 'login-user@example.com',
            'password' => Hash::make('correctpassword'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email' => 'login-user@example.com',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login_failed',
            'entity_type' => 'User',
        ]);
    }

    public function test_too_many_failed_logins_create_lockout_audit_log(): void
    {
        User::query()->create([
            'name' => 'Locked User',
            'email' => 'locked-user@example.com',
            'password' => Hash::make('correctpassword'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        for ($i = 0; $i < 6; $i++) {
            $this->post('/login', [
                'email' => 'locked-user@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'auth.login_locked',
            'entity_type' => 'User',
        ]);
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $user = User::query()->create([
            'name' => 'Patient',
            'email' => 'patient2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_admin_appointment_status_update_creates_audit_log(): void
    {
        $admin = User::query()->create([
            'name' => 'Audit Admin',
            'email' => 'audit-admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Audit Department',
            'description' => 'Audit Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Dr Audit',
            'specialty' => 'Cardiology',
            'qualification' => 'MBBS',
            'experience_years' => 8,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => 'Audit Patient',
            'patient_email' => 'audit-patient@example.com',
            'patient_phone' => '01712121212',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(10)->setMinute(0),
            'status' => 'pending',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Audit test',
        ]);

        $this->actingAs($admin)
            ->patch('/admin/appointments/'.$appointment->id, [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->id,
            'action' => 'appointment.status_updated',
            'entity_type' => 'Appointment',
            'entity_id' => $appointment->id,
        ]);

        $log = AuditLog::query()->where('entity_id', $appointment->id)->latest()->first();
        $this->assertSame('pending', $log?->old_values['status'] ?? null);
        $this->assertSame('confirmed', $log?->new_values['status'] ?? null);
    }

    public function test_admin_can_export_audit_logs_as_csv(): void
    {
        $admin = User::query()->create([
            'name' => 'CSV Admin',
            'email' => 'csv-admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        AuditLog::query()->create([
            'actor_user_id' => $admin->id,
            'action' => 'auth.login_success',
            'entity_type' => 'User',
            'entity_id' => $admin->id,
            'meta' => ['role' => 'admin'],
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/audit-logs/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_booking_with_payment_creates_payment_record(): void
    {
        $patient = User::query()->create([
            'name' => 'Patient Book',
            'email' => 'book@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Cardiology',
            'description' => 'Cardiology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Dr Payment',
            'specialty' => 'Cardiology',
            'qualification' => 'MBBS',
            'experience_years' => 10,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'available_days' => [strtolower(Carbon::tomorrow()->format('l'))],
            'available_from' => '09:00:00',
            'available_to' => '18:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $scheduled = Carbon::tomorrow()->setHour(10)->setMinute(0)->setSecond(0);

        $this->actingAs($patient)
            ->post('/appointments', [
                'doctor_id' => $doctor->id,
                'department_id' => $department->id,
                'patient_name' => 'Patient Book',
                'patient_email' => 'book@example.com',
                'patient_phone' => '01700000000',
                'service_mode' => 'online',
                'scheduled_for' => $scheduled->toDateTimeString(),
                'payment_method' => 'bkash',
                'reason' => 'Follow up',
                'notes' => 'N/A',
            ])
            ->assertRedirect('/');

        $this->assertDatabaseHas('appointments', [
            'doctor_id' => $doctor->id,
            'user_id' => $patient->id,
            'payment_method' => 'bkash',
            'payment_status' => 'pending',
        ]);

        $this->assertDatabaseHas('payments', [
            'user_id' => $patient->id,
            'method' => 'bkash',
            'status' => 'pending',
        ]);

        $this->assertGreaterThan(0, Payment::query()->count());
    }

    public function test_staff_login_redirects_to_staff_dashboard(): void
    {
        User::query()->create([
            'name' => 'Staff User',
            'email' => 'staff-login@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email' => 'staff-login@example.com',
            'password' => 'password123',
        ])->assertRedirect('/staff');
    }

    public function test_patient_can_cancel_own_pending_appointment(): void
    {
        $patient = User::query()->create([
            'name' => 'Cancel Patient',
            'email' => 'cancel@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Orthopedics',
            'description' => 'Orthopedics Department',
            'service_scope' => 'offline and online booking',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'name' => 'Dr Cancel',
            'specialty' => 'Orthopedics',
            'qualification' => 'MBBS',
            'experience_years' => 8,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'available_days' => [strtolower(Carbon::tomorrow()->format('l'))],
            'available_from' => '09:00:00',
            'available_to' => '18:00:00',
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = \App\Models\Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => 'Cancel Patient',
            'patient_email' => 'cancel@example.com',
            'patient_phone' => '01711111111',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(11)->setMinute(0)->setSecond(0),
            'status' => 'pending',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Checkup',
        ]);

        $this->actingAs($patient)
            ->patch('/my/appointments/'.$appointment->id, [
                'action' => 'cancel',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_doctor_can_update_own_online_and_offline_schedule(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor User',
            'email' => 'doctor-schedule@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Neurology',
            'description' => 'Neurology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Schedule',
            'specialty' => 'Neurology',
            'qualification' => 'MBBS',
            'experience_years' => 11,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1100,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $this->actingAs($doctorUser)
            ->patch('/doctor/schedule', [
                'online_available' => '1',
                'online_available_days' => ['monday', 'tuesday'],
                'online_available_from' => '10:00',
                'online_available_to' => '12:00',
                'offline_available' => '1',
                'offline_available_days' => ['wednesday', 'thursday'],
                'offline_available_from' => '15:00',
                'offline_available_to' => '19:00',
                'slot_minutes' => 20,
            ])
            ->assertRedirect();

        $doctor->refresh();
        $this->assertSame(['monday', 'tuesday'], $doctor->online_available_days);
        $this->assertSame(['wednesday', 'thursday'], $doctor->offline_available_days);
        $this->assertSame(20, $doctor->slot_minutes);
    }

    public function test_doctor_dashboard_can_filter_next_and_past_appointments(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Filter',
            'email' => 'doctor-filter@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Filter Department',
            'description' => 'Filter Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Filter',
            'specialty' => 'Medicine',
            'qualification' => 'MBBS',
            'experience_years' => 9,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => 'Past Patient',
            'patient_email' => 'past@example.com',
            'patient_phone' => '01710101010',
            'service_mode' => 'online',
            'scheduled_for' => \Carbon\Carbon::now()->startOfDay()->subDay()->addHours(12),
            'status' => 'completed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Past visit',
        ]);

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => 'Next Patient',
            'patient_email' => 'next@example.com',
            'patient_phone' => '01720202020',
            'service_mode' => 'online',
            'scheduled_for' => \Carbon\Carbon::now()->startOfDay()->addDay()->addHours(12),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Next visit',
        ]);

        Appointment::query()->create([
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => 'Today Patient',
            'patient_email' => 'today@example.com',
            'patient_phone' => '01730303030',
            'service_mode' => 'online',
            'scheduled_for' => \Carbon\Carbon::now()->startOfDay()->addHours(12),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Today visit',
        ]);

        $this->actingAs($doctorUser)
            ->get('/doctor?appointment_filter=today')
            ->assertOk()
            ->assertSee('Today Patient')
            ->assertDontSee('Past Patient');

        $this->actingAs($doctorUser)
            ->get('/doctor?appointment_filter=next')
            ->assertOk()
            ->assertSee('Next Patient')
            ->assertSee('Today Patient')
            ->assertDontSee('Past Patient');

        $this->actingAs($doctorUser)
            ->get('/doctor?appointment_filter=past')
            ->assertOk()
            ->assertSee('Past Patient')
            ->assertDontSee('Next Patient');
    }

    public function test_doctor_can_write_online_prescription_and_patient_can_view_it(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Prescriber',
            'email' => 'doctor-prescribe@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Prescription Patient',
            'email' => 'prescription-patient@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'ENT',
            'description' => 'ENT Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Prescriber',
            'specialty' => 'ENT',
            'qualification' => 'MBBS',
            'experience_years' => 9,
            'consultation_fee' => 900,
            'online_fee' => 800,
            'offline_fee' => 900,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01788888888',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(9)->setMinute(0)->setSecond(0),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Online follow-up',
        ]);

        $this->actingAs($doctorUser)
            ->patch('/doctor/appointments/'.$appointment->id.'/prescription', [
                'prescription_diagnosis' => 'Upper respiratory tract infection',
                'prescription_medicines' => "Paracetamol 500mg - 1+1+1 for 3 days",
                'prescription_advice' => 'Drink warm fluids and rest well.',
                'prescription_follow_up_date' => Carbon::tomorrow()->addDays(7)->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'prescription_diagnosis' => 'Upper respiratory tract infection',
        ]);

        $this->actingAs($patient)
            ->get('/my/appointments/'.$appointment->id)
            ->assertOk()
            ->assertSee('Download prescription PDF');
    }

    public function test_doctor_can_save_structured_prescription_items_with_medicine_mapping(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Structured',
            'email' => 'doctor-structured@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Patient Structured',
            'email' => 'patient-structured@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Medicine Unit',
            'description' => 'Medicine Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Structured',
            'specialty' => 'General Medicine',
            'qualification' => 'MBBS',
            'experience_years' => 10,
            'consultation_fee' => 900,
            'online_fee' => 850,
            'offline_fee' => 900,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'Napa',
            'medicine_group' => 'Paracetamol',
            'power' => '500mg',
            'amount' => '10 tablets',
            'price' => 20,
            'stock_quantity' => 100,
            'is_active' => true,
            'requires_prescription' => false,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01712312312',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(9)->setMinute(30)->setSecond(0),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Structured prescription check',
        ]);

        $this->actingAs($doctorUser)
            ->patch('/doctor/appointments/'.$appointment->id.'/prescription', [
                'prescription_diagnosis' => 'Fever',
                'prescription_medicines' => 'Take rest',
                'prescription_advice' => 'Hydration and sleep',
                'prescription_items' => [
                    [
                        'medicine_id' => $medicine->id,
                        'medicine_name' => 'Napa',
                        'amount' => '1 tablet',
                        'dosage' => '1+1+1',
                        'intake_time' => 'After meal',
                        'instructions' => 'For 3 days',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('appointment_prescription_items', [
            'appointment_id' => $appointment->id,
            'medicine_id' => $medicine->id,
            'medicine_name' => 'Napa',
            'dosage' => '1+1+1',
        ]);

        $this->actingAs($patient)
            ->get('/my/appointments/'.$appointment->id)
            ->assertOk()
            ->assertSee('Buy all prescribed medicines');
    }

    public function test_staff_can_create_new_doctor_with_initial_password(): void
    {
        $staff = User::query()->create([
            'name' => 'Staff Creator',
            'email' => 'staff-creator@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Nephrology',
            'description' => 'Nephrology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->post('/admin/doctors', [
                'doctor_email' => 'newdoctor@example.com',
                'initial_password' => 'newdoctor123',
                'department_id' => $department->id,
                'name' => 'Dr New Join',
                'specialty' => 'Nephrology',
                'qualification' => 'MBBS',
                'experience_years' => 5,
                'consultation_fee' => 1000,
                'online_fee' => 900,
                'offline_fee' => 1000,
                'slot_minutes' => 30,
                'online_available' => '1',
                'offline_available' => '1',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/doctors');

        $doctorUser = User::query()->where('email', 'newdoctor@example.com')->first();
        $this->assertNotNull($doctorUser);
        $this->assertSame('doctor', $doctorUser->role);

        $doctor = Doctor::query()->where('user_id', $doctorUser->id)->first();
        $this->assertNotNull($doctor);

        auth()->logout();

        $this->post('/login', [
            'email' => 'newdoctor@example.com',
            'password' => 'newdoctor123',
        ])->assertRedirect('/doctor');
    }

    public function test_patient_can_rate_doctor_and_comment_on_article(): void
    {
        $patient = User::query()->create([
            'name' => 'Reviewer Patient',
            'email' => 'reviewer-patient@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $author = User::query()->create([
            'name' => 'Author',
            'email' => 'author@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Review Dept',
            'description' => 'Review Dept',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $author->id,
            'name' => 'Dr Review',
            'specialty' => 'General',
            'experience_years' => 5,
            'consultation_fee' => 500,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $category = ArticleCategory::query()->create([
            'name' => 'Health',
            'slug' => 'health',
            'is_active' => true,
        ]);

        $article = Article::query()->create([
            'article_category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Reviewable Article',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'is_published' => true,
            'publication_status' => 'published',
        ]);

        $this->actingAs($patient)
            ->post('/doctors/'.$doctor->slug.'/reviews', [
                'rating' => 5,
                'comment' => 'Excellent support',
            ])
            ->assertRedirect();

        $this->actingAs($patient)
            ->post('/articles/'.$article->id.'/comments', [
                'rating' => 4,
                'comment' => 'Helpful article',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('doctor_reviews', [
            'doctor_id' => $doctor->id,
            'user_id' => $patient->id,
            'rating' => 5,
        ]);

        $this->assertDatabaseHas('article_comments', [
            'article_id' => $article->id,
            'user_id' => $patient->id,
        ]);
    }

    public function test_patient_can_ask_qna_and_doctor_can_answer(): void
    {
        $patient = User::query()->create([
            'name' => 'Qna Patient',
            'email' => 'qna-patient@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $doctor = User::query()->create([
            'name' => 'Qna Doctor',
            'email' => 'qna-doctor@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $this->actingAs($patient)
            ->post('/qna', [
                'title' => 'Can I take this medicine before meal?',
                'question' => 'Need guidance for dosage timing.',
            ])
            ->assertRedirect();

        $questionId = \App\Models\QnaQuestion::query()->latest()->value('id');

        $this->actingAs($doctor)
            ->post('/qna/'.$questionId.'/answers', [
                'answer' => 'Take it after meal for better tolerance.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('qna_answers', [
            'qna_question_id' => $questionId,
            'user_id' => $doctor->id,
        ]);
    }

    public function test_patient_can_add_all_prescribed_medicines_to_cart_from_route(): void
    {
        $patient = User::query()->create([
            'name' => 'PDF Buyer',
            'email' => 'pdf-buyer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $doctorUser = User::query()->create([
            'name' => 'Doctor PDF',
            'email' => 'doctor-pdf@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'PDF Department',
            'description' => 'Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr PDF',
            'specialty' => 'General',
            'experience_years' => 5,
            'consultation_fee' => 700,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $medicine = Medicine::query()->create([
            'name' => 'PDF Medicine',
            'medicine_group' => 'Group',
            'power' => '500mg',
            'amount' => '1 strip',
            'strength' => '500mg',
            'price' => 50,
            'stock_quantity' => 10,
            'is_active' => true,
            'requires_prescription' => false,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01741414141',
            'service_mode' => 'online',
            'scheduled_for' => now()->addDay(),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Route add test',
        ]);

        $appointment->prescriptionItems()->create([
            'medicine_id' => $medicine->id,
            'medicine_name' => $medicine->name,
            'amount' => '1 tablet',
            'dosage' => '1+0+1',
            'intake_time' => 'After meal',
            'sort_order' => 1,
        ]);

        $this->actingAs($patient)
            ->get('/my/appointments/'.$appointment->id.'/buy-all-medicines')
            ->assertRedirect('/shop/cart')
            ->assertSessionHas('medicine_cart', function ($cart) use ($medicine) {
                return isset($cart[$medicine->id]) && (int) $cart[$medicine->id] >= 1;
            });
    }

    public function test_admin_can_create_new_staff_with_initial_password(): void
    {
        $admin = User::query()->create([
            'name' => 'Main Admin',
            'email' => 'main-admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/staff', [
                'name' => 'Created Staff',
                'email' => 'created-staff@example.com',
                'initial_password' => 'createdstaff123',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin');

        $this->assertDatabaseHas('users', [
            'email' => 'created-staff@example.com',
            'role' => 'staff',
        ]);

        auth()->logout();

        $this->post('/login', [
            'email' => 'created-staff@example.com',
            'password' => 'createdstaff123',
        ])->assertRedirect('/staff');
    }

    public function test_doctor_can_change_password_after_first_login(): void
    {
        $doctor = User::query()->create([
            'name' => 'Password Doctor',
            'email' => 'password-doctor@example.com',
            'password' => Hash::make('initialpass123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Endocrinology',
            'description' => 'Endocrinology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctor->id,
            'name' => 'Dr Password',
            'specialty' => 'Endocrinology',
            'qualification' => 'MBBS',
            'experience_years' => 5,
            'consultation_fee' => 900,
            'online_fee' => 800,
            'offline_fee' => 900,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $this->actingAs($doctor)
            ->patch('/doctor/password', [
                'current_password' => 'initialpass123',
                'new_password' => 'newsecurepass123',
                'new_password_confirmation' => 'newsecurepass123',
            ])
            ->assertRedirect();

        auth()->logout();

        $this->post('/login', [
            'email' => 'password-doctor@example.com',
            'password' => 'newsecurepass123',
        ])->assertRedirect('/doctor');
    }

    public function test_patient_and_doctor_can_chat_after_confirmation(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Chat',
            'email' => 'doctor-chat@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Patient Chat',
            'email' => 'patient-chat@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Dermatology',
            'description' => 'Dermatology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Chat',
            'specialty' => 'Dermatology',
            'qualification' => 'MBBS',
            'experience_years' => 7,
            'consultation_fee' => 900,
            'online_fee' => 800,
            'offline_fee' => 900,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01799999999',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(15)->setMinute(0)->setSecond(0),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Skin check',
        ]);

        $this->actingAs($patient)
            ->post('/appointments/'.$appointment->id.'/chat', [
                'message' => 'Hello doctor, I have an update.',
            ])
            ->assertRedirect();

        $this->actingAs($doctorUser)
            ->post('/appointments/'.$appointment->id.'/chat', [
                'message' => 'Thanks, please continue your medicines.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('appointment_chat_messages', [
            'appointment_id' => $appointment->id,
            'user_id' => $patient->id,
            'message' => 'Hello doctor, I have an update.',
        ]);

        $this->assertDatabaseHas('appointment_chat_messages', [
            'appointment_id' => $appointment->id,
            'user_id' => $doctorUser->id,
            'message' => 'Thanks, please continue your medicines.',
        ]);
    }

    public function test_chat_is_blocked_for_non_confirmed_appointment(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Pending Chat',
            'email' => 'doctor-pending-chat@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Patient Pending Chat',
            'email' => 'patient-pending-chat@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Urology',
            'description' => 'Urology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Pending Chat',
            'specialty' => 'Urology',
            'qualification' => 'MBBS',
            'experience_years' => 5,
            'consultation_fee' => 850,
            'online_fee' => 750,
            'offline_fee' => 850,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01766666666',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(16)->setMinute(0)->setSecond(0),
            'status' => 'pending',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Consultation',
        ]);

        $this->actingAs($patient)
            ->from('/my/appointments/'.$appointment->id)
            ->post('/appointments/'.$appointment->id.'/chat', [
                'message' => 'Can we chat now?',
            ])
            ->assertRedirect('/my/appointments/'.$appointment->id)
            ->assertSessionHasErrors('message');

        $this->assertDatabaseMissing('appointment_chat_messages', [
            'appointment_id' => $appointment->id,
            'message' => 'Can we chat now?',
        ]);
    }

    public function test_chat_message_read_status_updates_after_receiver_marks_read(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Read',
            'email' => 'doctor-read@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Patient Read',
            'email' => 'patient-read@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Oncology',
            'description' => 'Oncology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Read',
            'specialty' => 'Oncology',
            'qualification' => 'MBBS',
            'experience_years' => 6,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01733333333',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(17)->setMinute(0)->setSecond(0),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Treatment discussion',
        ]);

        $this->actingAs($patient)
            ->post('/appointments/'.$appointment->id.'/chat', [
                'message' => 'Please check my report.',
            ])
            ->assertRedirect();

        $this->actingAs($doctorUser)
            ->post('/appointments/'.$appointment->id.'/chat/read')
            ->assertOk();

        $this->assertDatabaseHas('appointment_chat_messages', [
            'appointment_id' => $appointment->id,
            'message' => 'Please check my report.',
        ]);

        $this->assertNotNull($appointment->chatMessages()->first()?->fresh()->read_at);
    }

    public function test_chat_message_can_include_file_attachment(): void
    {
        Storage::fake('public');

        $doctorUser = User::query()->create([
            'name' => 'Doctor Attachment',
            'email' => 'doctor-attachment@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $patient = User::query()->create([
            'name' => 'Patient Attachment',
            'email' => 'patient-attachment@example.com',
            'password' => Hash::make('password123'),
            'role' => 'patient',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Pediatrics',
            'description' => 'Pediatrics Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Attachment',
            'specialty' => 'Pediatrics',
            'qualification' => 'MBBS',
            'experience_years' => 4,
            'consultation_fee' => 700,
            'online_fee' => 650,
            'offline_fee' => 700,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'department_id' => $department->id,
            'patient_name' => $patient->name,
            'patient_email' => $patient->email,
            'patient_phone' => '01744444444',
            'service_mode' => 'online',
            'scheduled_for' => Carbon::tomorrow()->setHour(10)->setMinute(30)->setSecond(0),
            'status' => 'confirmed',
            'payment_method' => 'none',
            'payment_status' => 'not_required',
            'reason' => 'Follow-up',
        ]);

        $this->actingAs($patient)
            ->post('/appointments/'.$appointment->id.'/chat', [
                'attachment' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect();

        $message = $appointment->chatMessages()->first();
        $this->assertNotNull($message);
        $this->assertNotNull($message->attachment_path);
        Storage::disk('public')->assertExists($message->attachment_path);
    }

    public function test_doctor_can_submit_article_for_review_and_admin_can_publish(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Writer',
            'email' => 'doctor-writer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $admin = User::query()->create([
            'name' => 'Admin Reviewer',
            'email' => 'admin-reviewer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = ArticleCategory::query()->create([
            'name' => 'Clinical Guidance',
            'slug' => 'clinical-guidance',
            'description' => 'Clinical and health guidance',
            'is_active' => true,
        ]);

        // doctor profile required for doctor panel access
        $department = Department::query()->create([
            'name' => 'Pulmonology',
            'description' => 'Pulmonology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Writer',
            'specialty' => 'Pulmonology',
            'qualification' => 'MBBS',
            'experience_years' => 5,
            'consultation_fee' => 900,
            'online_fee' => 800,
            'offline_fee' => 900,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $this->actingAs($doctorUser)
            ->post('/doctor/articles', [
                'article_category_id' => $category->id,
                'title' => 'Breathing care tips',
                'excerpt' => 'How patients can improve breathing capacity.',
                'body' => 'Clinical guidance body',
                'submit_action' => 'submit_review',
            ])
            ->assertRedirect('/doctor/articles');

        $article = Article::query()->where('title', 'Breathing care tips')->first();
        $this->assertNotNull($article);
        $this->assertSame('pending_review', $article->publication_status);
        $this->assertFalse((bool) $article->is_published);

        $this->actingAs($admin)
            ->patch('/admin/articles/'.$article->id.'/review', [
                'decision' => 'approve',
            ])
            ->assertRedirect();

        $article->refresh();
        $this->assertTrue((bool) $article->is_published);
        $this->assertSame('published', $article->publication_status);
    }

    public function test_staff_can_review_and_reject_doctor_article(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor Author',
            'email' => 'doctor-author@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $staff = User::query()->create([
            'name' => 'Staff Reviewer',
            'email' => 'staff-reviewer@example.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        $category = ArticleCategory::query()->create([
            'name' => 'Wellness',
            'slug' => 'wellness',
            'description' => 'Wellness content',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Neuromedicine',
            'description' => 'Neuromedicine Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Author',
            'specialty' => 'Neuromedicine',
            'qualification' => 'MBBS',
            'experience_years' => 4,
            'consultation_fee' => 800,
            'online_fee' => 700,
            'offline_fee' => 800,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $article = Article::query()->create([
            'article_category_id' => $category->id,
            'user_id' => $doctorUser->id,
            'title' => 'Migraine care draft',
            'excerpt' => 'Draft excerpt',
            'body' => 'Draft body',
            'is_featured' => false,
            'is_published' => false,
            'publication_status' => 'pending_review',
        ]);

        $this->actingAs($staff)
            ->patch('/admin/articles/'.$article->id.'/review', [
                'decision' => 'reject',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'publication_status' => 'rejected',
            'is_published' => 0,
        ]);
    }

    public function test_admin_can_add_department_from_admin_panel(): void
    {
        $admin = User::query()->create([
            'name' => 'Department Admin',
            'email' => 'department-admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post('/admin/departments', [
                'name' => 'Gastroenterology',
                'description' => 'Digestive health and liver care.',
                'service_scope' => 'online and offline',
                'is_active' => '1',
            ])
            ->assertRedirect('/admin/departments');

        $this->assertDatabaseHas('departments', [
            'name' => 'Gastroenterology',
            'service_scope' => 'online and offline',
            'is_active' => 1,
        ]);
    }

    public function test_published_article_shows_writer_doctor_name(): void
    {
        $doctorUser = User::query()->create([
            'name' => 'Doctor User Name',
            'email' => 'doctor-writer-name@example.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
            'is_active' => true,
        ]);

        $department = Department::query()->create([
            'name' => 'Rheumatology',
            'description' => 'Rheumatology Department',
            'service_scope' => 'online and offline',
            'is_active' => true,
        ]);

        $doctor = Doctor::query()->create([
            'department_id' => $department->id,
            'user_id' => $doctorUser->id,
            'name' => 'Dr Writer Name',
            'specialty' => 'Rheumatology',
            'qualification' => 'MBBS',
            'experience_years' => 7,
            'consultation_fee' => 1000,
            'online_fee' => 900,
            'offline_fee' => 1000,
            'online_available' => true,
            'offline_available' => true,
            'slot_minutes' => 30,
            'is_active' => true,
        ]);

        $category = ArticleCategory::query()->create([
            'name' => 'Doctor Insights',
            'slug' => 'doctor-insights',
            'description' => 'Doctor-authored insights',
            'is_active' => true,
        ]);

        $article = Article::query()->create([
            'article_category_id' => $category->id,
            'user_id' => $doctorUser->id,
            'title' => 'Joint pain guidance',
            'excerpt' => 'Short guidance excerpt.',
            'body' => 'Detailed doctor-authored guidance.',
            'is_featured' => false,
            'is_published' => true,
            'publication_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/articles/'.$article->slug)
            ->assertOk()
            ->assertSee('Writer doctor: '.$doctor->name);
    }
}

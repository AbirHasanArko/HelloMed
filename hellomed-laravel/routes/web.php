<?php

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminDepartmentController;
use App\Http\Controllers\Admin\AdminDoctorController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AvailableTestController as AdminAvailableTestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\ArticleController as DoctorArticleController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Frontend\AppointmentController;
use App\Http\Controllers\Frontend\AppointmentChatController;
use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\ArticleCommentController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\DepartmentController;
use App\Http\Controllers\Frontend\AvailableTestController as FrontendAvailableTestController;
use App\Http\Controllers\Frontend\DoctorReviewController;
use App\Http\Controllers\Frontend\DoctorController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MedicineCartController;
use App\Http\Controllers\Frontend\MedicineController;
use App\Http\Controllers\Frontend\PatientAppointmentPrescriptionPdfController;
use App\Http\Controllers\Frontend\MedicinePaymentController;
use App\Http\Controllers\Frontend\PatientDashboardController;
use App\Http\Controllers\Frontend\PatientRecordController;
use App\Http\Controllers\Frontend\PrescriptionCartController;
use App\Http\Controllers\Frontend\QnaController;
use App\Http\Controllers\Frontend\PatientMedicineInvoiceController;
use App\Http\Controllers\Frontend\PatientMedicineOrderController;
use App\Http\Controllers\Pharmacist\DashboardController as PharmacistDashboardController;
use App\Http\Controllers\Pharmacist\MedicineController as PharmacistMedicineController;
use App\Http\Controllers\Pharmacist\OrderController as PharmacistOrderController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LabTestController as StaffLabTestController;
use App\Http\Controllers\LabTestDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::get('/departments/{department:slug}', [DepartmentController::class, 'show'])->name('departments.show');
Route::get('/lab-tests', [FrontendAvailableTestController::class, 'index'])->name('available-tests.index');
Route::get('/lab-tests/{availableTest:slug}', [FrontendAvailableTestController::class, 'show'])->name('available-tests.show');
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{doctor:slug}', [DoctorController::class, 'show'])->name('doctors.show');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/qna', [QnaController::class, 'index'])->name('qna.index');
Route::get('/qna/{question}', [QnaController::class, 'show'])->name('qna.show');
Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
Route::get('/medicines/{medicine:slug}', [MedicineController::class, 'show'])->name('medicines.show');
Route::get('/appointments/create/{doctor:slug}', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/contact', ContactController::class)->name('contact');
Route::get('/shop/cart', [MedicineCartController::class, 'index'])->name('shop.cart');
Route::post('/shop/cart/{medicine}', [MedicineCartController::class, 'add'])->name('shop.cart.add');
Route::patch('/shop/cart/{medicine}', [MedicineCartController::class, 'update'])->name('shop.cart.update');
Route::delete('/shop/cart/{medicine}', [MedicineCartController::class, 'remove'])->name('shop.cart.remove');

Route::get('/ambulance', [\App\Http\Controllers\Frontend\AmbulanceController::class, 'create'])->name('ambulance.create');
Route::post('/ambulance', [\App\Http\Controllers\Frontend\AmbulanceController::class, 'store'])->name('ambulance.store');

Route::get('/api/doctors/{doctor}/schedule', [\App\Http\Controllers\Api\DoctorScheduleController::class, 'show'])->name('api.doctors.schedule');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/shop/checkout', [MedicineCartController::class, 'checkout'])->middleware('auth')->name('shop.checkout');
Route::get('/my/appointments', [PatientDashboardController::class, 'index'])
    ->middleware('auth')
    ->name('patient.appointments');
Route::get('/my/appointments/{appointment}', [PatientDashboardController::class, 'show'])
    ->middleware('auth')
    ->name('patient.appointments.show');
Route::get('/my/appointments/{appointment}/prescription-pdf', PatientAppointmentPrescriptionPdfController::class)
    ->middleware('auth')
    ->name('patient.appointments.prescription-pdf');
Route::get('/my/appointments/{appointment}/buy-all-medicines', [PrescriptionCartController::class, 'addAll'])
    ->middleware('auth')
    ->name('patient.appointments.buy-all-medicines');
Route::patch('/my/appointments/{appointment}', [PatientDashboardController::class, 'update'])
    ->middleware('auth')
    ->name('patient.appointments.update');
Route::get('/my/profile', [PatientDashboardController::class, 'profile'])
    ->middleware('auth')
    ->name('patient.profile');
Route::patch('/my/profile', [PatientDashboardController::class, 'updateProfile'])
    ->middleware('auth')
    ->name('patient.profile.update');
Route::get('/my/records', [PatientRecordController::class, 'index'])
    ->middleware('auth')
    ->name('patient.records');
Route::post('/doctors/{doctor:slug}/reviews', [DoctorReviewController::class, 'store'])
    ->middleware('auth')
    ->name('doctors.reviews.store');
Route::post('/articles/{article}/comments', [ArticleCommentController::class, 'store'])
    ->middleware('auth')
    ->name('articles.comments.store');
Route::post('/qna', [QnaController::class, 'storeQuestion'])
    ->middleware('auth')
    ->name('qna.store');
Route::post('/qna/{question}/answers', [QnaController::class, 'storeAnswer'])
    ->middleware('auth')
    ->name('qna.answers.store');
Route::post('/appointments/{appointment}/chat', [AppointmentChatController::class, 'store'])
    ->middleware('auth')
    ->name('appointments.chat.store');
Route::get('/appointments/{appointment}/chat/messages', [AppointmentChatController::class, 'index'])
    ->middleware('auth')
    ->name('appointments.chat.messages');
Route::post('/appointments/{appointment}/chat/read', [AppointmentChatController::class, 'markRead'])
    ->middleware('auth')
    ->name('appointments.chat.read');
Route::get('/my/medicine-orders', [PatientMedicineOrderController::class, 'index'])
    ->middleware('auth')
    ->name('patient.medicine-orders');
Route::get('/my/medicine-orders/{order}', [PatientMedicineOrderController::class, 'show'])
    ->middleware('auth')
    ->name('patient.medicine-orders.show');
Route::get('/my/medicine-orders/{order}/invoice', PatientMedicineInvoiceController::class)
    ->middleware('auth')
    ->name('patient.medicine-orders.invoice');
Route::get('/shop/payments/{order}/{provider}', [MedicinePaymentController::class, 'start'])
    ->middleware('auth')
    ->name('shop.payments.start');
Route::get('/shop/payments/{order}/{provider}/callback/{status}', [MedicinePaymentController::class, 'callback'])
    ->middleware('auth')
    ->name('shop.payments.callback');
Route::get('/lab-tests/{labTest}/download', LabTestDownloadController::class)
    ->middleware('auth')
    ->name('lab-tests.download');

Route::prefix('pharmacist')
    ->name('pharmacist.')
    ->middleware(['auth', 'role:pharmacist'])
    ->group(function (): void {
        Route::get('/', [PharmacistDashboardController::class, 'index'])->name('dashboard');
        Route::get('/medicines', [PharmacistMedicineController::class, 'index'])->name('medicines.index');
        Route::get('/medicines/create', [PharmacistMedicineController::class, 'create'])->name('medicines.create');
        Route::post('/medicines', [PharmacistMedicineController::class, 'store'])->name('medicines.store');
        Route::get('/medicines/{medicine}/edit', [PharmacistMedicineController::class, 'edit'])->name('medicines.edit');
        Route::put('/medicines/{medicine}', [PharmacistMedicineController::class, 'update'])->name('medicines.update');
        Route::get('/orders', [PharmacistOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}/prescription', [PharmacistOrderController::class, 'prescription'])->name('orders.prescription');
        Route::patch('/orders/{order}', [PharmacistOrderController::class, 'update'])->name('orders.update');
    });

Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'role:staff'])
    ->group(function (): void {
        Route::get('/', [StaffDashboardController::class, 'index'])->name('dashboard');
        Route::get('/ambulance', [\App\Http\Controllers\Staff\AmbulanceController::class, 'index'])->name('ambulance.index');
        Route::patch('/ambulance/{ambulanceRequest}', [\App\Http\Controllers\Staff\AmbulanceController::class, 'update'])->name('ambulance.update');
        Route::get('/offline-appointments', [\App\Http\Controllers\Staff\OfflineAppointmentController::class, 'create'])->name('offline-appointments.create');
        Route::post('/offline-appointments', [\App\Http\Controllers\Staff\OfflineAppointmentController::class, 'store'])->name('offline-appointments.store');
        Route::get('/lab-tests', [StaffLabTestController::class, 'index'])->name('lab-tests.index');
        Route::post('/lab-tests/{labTest}/upload', [StaffLabTestController::class, 'upload'])->name('lab-tests.upload');
    });

Route::prefix('doctor')
    ->name('doctor.')
    ->middleware(['auth', 'role:doctor'])
    ->group(function (): void {
        Route::get('/', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/schedule', [DoctorDashboardController::class, 'updateSchedule'])->name('schedule.update');
        Route::patch('/password', [DoctorDashboardController::class, 'updatePassword'])->name('password.update');
        Route::get('/articles', [DoctorArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [DoctorArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [DoctorArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}/edit', [DoctorArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{article}', [DoctorArticleController::class, 'update'])->name('articles.update');
        Route::get('/appointments/{appointment}', [DoctorAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}/meeting-link', [DoctorAppointmentController::class, 'updateMeetingLink'])->name('appointments.meeting-link.update');
        Route::patch('/appointments/{appointment}/prescription', [DoctorAppointmentController::class, 'updatePrescription'])->name('appointments.prescription.update');
        Route::patch('/appointments/{appointment}/patient-profile', [DoctorAppointmentController::class, 'updatePatientProfile'])->name('appointments.patient-profile.update');
        Route::post('/appointments/{appointment}/lab-tests', [DoctorAppointmentController::class, 'storeLabTest'])->name('appointments.lab-tests.store');
        Route::delete('/lab-tests/{labTest}', [DoctorAppointmentController::class, 'destroyLabTest'])->name('lab-tests.destroy');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin,staff'])
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::get('/patients/{user}/profile/edit', [\App\Http\Controllers\Admin\AdminPatientProfileController::class, 'edit'])->name('patients.profile.edit');
        Route::patch('/patients/{user}/profile', [\App\Http\Controllers\Admin\AdminPatientProfileController::class, 'update'])->name('patients.profile.update');
        Route::get('/departments', [AdminDepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/create', [AdminDepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [AdminDepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{department}/edit', [AdminDepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [AdminDepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [AdminDepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::resource('available-tests', AdminAvailableTestController::class)->except(['show']);

        Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
        Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
        Route::patch('/articles/{article}/review', [AdminArticleController::class, 'review'])->name('articles.review');
        Route::get('/doctors', [AdminDoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/create', [AdminDoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors', [AdminDoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{doctor}/edit', [AdminDoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('/doctors/{doctor}', [AdminDoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{doctor}', [AdminDoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');

        Route::middleware('role:admin')->group(function (): void {
            Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
            Route::patch('/payments/{payment}', [AdminPaymentController::class, 'update'])->name('payments.update');
            Route::get('/staff', [AdminStaffController::class, 'index'])->name('staff.index');
            Route::get('/staff/create', [AdminStaffController::class, 'create'])->name('staff.create');
            Route::post('/staff', [AdminStaffController::class, 'store'])->name('staff.store');
            Route::delete('/staff/{user}', [AdminStaffController::class, 'destroy'])->name('staff.destroy');
        });
    });

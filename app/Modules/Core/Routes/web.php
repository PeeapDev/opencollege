<?php

use App\Modules\Core\Controllers\AuthController;
use App\Modules\Core\Controllers\DashboardController;
use App\Modules\Core\Controllers\SuperAdminController;
use App\Modules\Core\Controllers\NsiVerificationController;
use App\Modules\Core\Controllers\CollegeRegistrationController;
use App\Modules\Core\Controllers\FrontendController;
use App\Modules\Student\Controllers\AdmissionController;
use App\Modules\Student\Controllers\StudentPortalController;
use Illuminate\Support\Facades\Route;

// Public frontend (college website)
Route::get('/', [FrontendController::class, 'index'])->name('frontend.home');
Route::get('/about', [FrontendController::class, 'about'])->name('frontend.about');
Route::get('/programs-list', [FrontendController::class, 'programs'])->name('frontend.programs');
Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');

// Public online admission
Route::get('/apply', [AdmissionController::class, 'publicForm'])->name('admission.apply');
Route::post('/apply', [AdmissionController::class, 'publicSubmit'])->middleware('throttle:10,1')->name('admission.submit');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public college registration (platform level)
Route::get('/register-college', [CollegeRegistrationController::class, 'showForm'])->name('college.register');
Route::post('/register-college', [CollegeRegistrationController::class, 'register'])->middleware('throttle:5,10');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // NSI Verification
    Route::get('/nsi-verification', [NsiVerificationController::class, 'index'])->name('nsi.index');
    Route::post('/nsi-verification/verify', [NsiVerificationController::class, 'verify'])->name('nsi.verify');
    Route::get('/nsi-verification/{id}', [NsiVerificationController::class, 'show'])->name('nsi.show');

    // Student Portal
    Route::get('/student-portal', [StudentPortalController::class, 'dashboard'])->name('student.portal');
    Route::get('/student-portal/profile', [StudentPortalController::class, 'profile'])->name('student.profile');
    Route::get('/student-portal/results', [StudentPortalController::class, 'results'])->name('student.results');
    Route::get('/student-portal/finances', [StudentPortalController::class, 'finances'])->name('student.finances');
    Route::get('/student-portal/id-card', [StudentPortalController::class, 'idCard'])->name('student.id_card');

    // ID Card Management (admin)
    Route::get('/id-cards', [StudentPortalController::class, 'idCardList'])->name('id_cards.index');
    Route::post('/id-cards/generate/{student}', [StudentPortalController::class, 'generateIdCard'])->name('id_cards.generate');
    Route::post('/id-cards/bulk-generate', [StudentPortalController::class, 'bulkGenerateIdCards'])->name('id_cards.bulk_generate');
    Route::get('/id-cards/{idCard}/print', [StudentPortalController::class, 'printIdCard'])->name('id_cards.print');

    // QR Code Scanner
    Route::get('/qr-scanner', [StudentPortalController::class, 'qrScanner'])->name('qr.scanner');
    Route::post('/qr-verify', [StudentPortalController::class, 'qrVerify'])->name('qr.verify');

    // Admissions Management (admin)
    Route::get('/admissions', [AdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/{admission}', [AdmissionController::class, 'show'])->name('admissions.show');
    Route::post('/admissions/{admission}/accept', [AdmissionController::class, 'accept'])->name('admissions.accept');
    Route::post('/admissions/{admission}/reject', [AdmissionController::class, 'reject'])->name('admissions.reject');
    Route::post('/admissions/{admission}/enroll', [AdmissionController::class, 'enroll'])->name('admissions.enroll');
});

// Super Admin routes
Route::middleware(['auth', 'super_admin'])->prefix('superadmin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/colleges', [SuperAdminController::class, 'colleges'])->name('superadmin.colleges');
    Route::get('/colleges/create', [SuperAdminController::class, 'createCollege'])->name('superadmin.colleges.create');
    Route::post('/colleges', [SuperAdminController::class, 'storeCollege'])->name('superadmin.colleges.store');
    Route::post('/colleges/{institution}/toggle', [SuperAdminController::class, 'toggleCollege'])->name('superadmin.colleges.toggle');
    Route::delete('/colleges/{institution}', [SuperAdminController::class, 'destroyCollege'])->name('superadmin.colleges.destroy');
});

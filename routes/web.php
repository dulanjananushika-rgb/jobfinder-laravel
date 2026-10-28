<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\FeaturedJobController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobAlertController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

// Public Static & Information Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// Company Showcases & Micro-Sites
Route::get('/companies', [CompanyController::class, 'index'])->name('companies');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

// Career Advice, Tips & Policies
Route::view('/career-advice', 'pages.career-advice')->name('career-advice');
Route::view('/resume-tips', 'pages.resume-tips')->name('resume-tips');
Route::view('/interview-tips', 'pages.interview-tips')->name('interview-tips');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// Job Directory & Safety
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/jobs/{job}/report', [JobReportController::class, 'store'])->middleware('throttle:5,1')->name('jobs.report');
Route::post('/job-alerts', [JobAlertController::class, 'store'])->middleware('throttle:5,1')->name('job-alerts.store');

// Guest Authentication, Password Reset & Social Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');

    // Password Reset Flow
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

    // Socialite OAuth Routes (Google & LinkedIn)
    Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Authenticated Core Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Email Verification Routes
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');

    // Secure Resume Download
    Route::get('/applications/{application}/resume', [ApplicationController::class, 'downloadResume'])->name('applications.resume');

    // In-App Notification Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Direct Messaging between Employer & Candidate
    Route::get('/applications/{application}/messages', [ChatController::class, 'show'])->name('applications.messages');
    Route::post('/applications/{application}/messages', [ChatController::class, 'store'])->name('applications.messages.store');
});

// Job Seeker Routes
Route::middleware(['auth', 'role:job_seeker'])->group(function () {
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}/confirmation', [ApplicationController::class, 'confirmation'])->name('applications.confirmation');
    Route::post('/jobs/{job}/save', [JobController::class, 'toggleSave'])->name('jobs.save');
    Route::get('/my-applications', [ApplicationController::class, 'seekerIndex'])->name('seeker.applications');
    Route::put('/my-applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('seeker.applications.withdraw');
    Route::get('/saved-jobs', fn () => view('seeker.saved-jobs', [
        'jobs' => auth()->user()->savedJobs()->with('employer')->latest()->paginate(10),
    ]))->name('seeker.saved-jobs');
});

// Employer Management Routes
Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::resource('jobs', EmployerJobController::class)->except('show');
    Route::post('/jobs/{job}/promote', [FeaturedJobController::class, 'promote'])->name('jobs.promote');
    Route::get('/applications', [ApplicationController::class, 'employerIndex'])->name('applications.index');
    Route::get('/applications/export', [ApplicationController::class, 'exportCsv'])->name('applications.export');
    Route::put('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::post('/applications/{application}/interview', [InterviewController::class, 'store'])->name('applications.interview');
});

// System Administrator Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');
    Route::put('/jobs/{job}', [AdminController::class, 'updateJob'])->name('jobs.update');
    Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('jobs.delete');
    Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::put('/messages/{message}', [AdminController::class, 'updateMessage'])->name('messages.update');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::put('/reports/{report}', [AdminController::class, 'updateReport'])->name('reports.update');
});

<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');
Route::view('/companies', 'pages.companies')->name('companies');
Route::view('/career-advice', 'pages.career-advice')->name('career-advice');
Route::view('/resume-tips', 'pages.resume-tips')->name('resume-tips');
Route::view('/interview-tips', 'pages.interview-tips')->name('interview-tips');
Route::view('/blog', 'pages.blog')->name('blog');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});

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

Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::resource('jobs', EmployerJobController::class)->except('show');
    Route::get('/applications', [ApplicationController::class, 'employerIndex'])->name('applications.index');
    Route::put('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.status');
});

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
});

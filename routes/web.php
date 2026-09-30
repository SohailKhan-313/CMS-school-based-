<?php

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Login Required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard (Main Overview for all interconnected models)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');
    Route::get('/home', [DashboardController::class, 'index']);

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ----------------------------------------------------
    // STUDENTS MODULE (CRUD + FPDF Reports)
    // ----------------------------------------------------
    Route::get('/students/export/pdf', [StudentController::class, 'exportPdf'])->name('students.pdf');
    Route::get('/students/{student}/slip/pdf', [StudentController::class, 'printSlip'])->name('students.slip');
    Route::resource('students', StudentController::class);
    // Backward compatibility for login redirect and sidebar
    Route::get('/student', function () {
        return redirect()->route('students.index');
    })->name('student');

    // ----------------------------------------------------
    // TEACHERS MODULE (CRUD + FPDF Reports)
    // ----------------------------------------------------
    Route::get('/teachers/export/pdf', [TeacherController::class, 'exportPdf'])->name('teachers.pdf');
    Route::get('/teachers/{teacher}/profile/pdf', [TeacherController::class, 'printProfile'])->name('teachers.profile');
    Route::resource('teachers', TeacherController::class);
    Route::get('/teacher', function () {
        return redirect()->route('teachers.index');
    })->name('teacher');

    // ----------------------------------------------------
    // ACCOUNTANT / FEES MODULE (CRUD + FPDF Challans/Reports)
    Route::get('/accountant/export/pdf', [AccountantController::class, 'exportPdf'])->name('accountant.pdf');
    Route::get('/accountant/{invoice}/challan/pdf', [AccountantController::class, 'printChallan'])->name('accountant.challan');
    Route::resource('accountant', AccountantController::class)->parameters(['accountant' => 'invoice']);
    Route::get('/accountant-home', function () {
        return redirect()->route('accountant.index');
    })->name('accountant');

    // ----------------------------------------------------
    // SCHOOL CLASSES MODULE (CRUD + FPDF Roster)
    // ----------------------------------------------------
    Route::get('/classes/export/pdf', [SchoolClassController::class, 'exportPdf'])->name('classes.pdf');
    Route::resource('classes', SchoolClassController::class);

    // ----------------------------------------------------
    // ADMIN MODULE (Overview + System Stats)
    // ----------------------------------------------------
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/export/users/pdf', [AdminController::class, 'exportUsersPdf'])->name('admin.users.pdf');

    // ----------------------------------------------------
    // SPATIE ROLES & PERMISSIONS MODULE
    // ----------------------------------------------------
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    // ----------------------------------------------------
    // USER MANAGEMENT MODULE
    // ----------------------------------------------------
    Route::resource('users', UserController::class);

});

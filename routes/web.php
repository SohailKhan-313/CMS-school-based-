<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\accountantController;
use App\Http\Controllers\adminController;
use App\Http\Controllers\teacherController;
use App\Http\Controllers\studentController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Public Routes (No Login Required)
|--------------------------------------------------------------------------
*/

// Welcome page
Route::get('/', function () {
    return redirect()->route('login'); // force login first
});

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Logout (must be authenticated)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Protected Routes (Login Required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboards
   Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('home');
    Route::get('/student', [studentController::class, 'index'])->name('student');
    Route::get('/admin', [adminController::class, 'index'])->name('admin');
    Route::get('/teacher', [teacherController::class, 'index'])->name('teacher');
    Route::get('/accountant', [accountantController::class, 'index'])->name('accountant');

    //Registration
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');

    // Profile
    Route::middleware('auth')->group(function () {
         Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
         Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

    // Permissions
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

});
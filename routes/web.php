<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\GroupDatabaseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('userlogin');
});


// =========================
// Authentication
// =========================

Route::get('/login', [LoginController::class, 'showUserLogin'])
    ->name('userlogin');

Route::post('/login', [LoginController::class, 'userLogin'])
    ->name('userlogin.submit');

Route::post('/logout', [LoginController::class, 'logoutUser'])
    ->name('userlogout');

Route::get('/admin/login', [LoginController::class, 'showAdminLogin'])
    ->name('login');

Route::post('/admin/login', [LoginController::class, 'adminLogin'])
    ->name('login.submit');

Route::post('/admin/logout', [LoginController::class, 'logoutAdmin'])
    ->name('logout');


// =========================
// Dashboard
// =========================
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // =========================
    // Student Management
    // =========================

    Route::get('/students/create', [StudentController::class, 'create'])
        ->name('students.create');

    Route::post('/students', [StudentController::class, 'store'])
        ->name('students.store');

    Route::get('/students/import', [StudentController::class, 'importPage'])
        ->name('students.import');

    Route::post('/students/import', [StudentController::class, 'import'])
        ->name('students.import.store');

    Route::get('/students/template', [StudentController::class, 'downloadTemplate'])
        ->name('students.template');

    Route::get('/students', [StudentController::class, 'index'])
        ->name('students.index');

    Route::get('/students/export', [StudentController::class, 'export'])
        ->name('students.export');

    Route::get('/students/{student}', [StudentController::class, 'show'])
        ->name('students.show');

    Route::delete('/students/{student}', [StudentController::class, 'destroy'])
        ->name('students.destroy');

    Route::put('/students/{student}/password', [StudentController::class, 'updatePassword'])
        ->name('students.updatePassword');


    // =========================
    // Database Management
    // =========================

    Route::get('/databases', [DatabaseController::class, 'index'])
        ->name('databases.index');

    Route::get('/databases/{student}', [DatabaseController::class, 'show'])
        ->name('databases.show');

    Route::post('/databases/{id}/grant', [DatabaseController::class, 'grantAccess'])
        ->name('databases.grant');

    Route::post('/databases/{id}/revoke', [DatabaseController::class, 'revokeAccess'])
        ->name('databases.revoke');

    Route::post('/databases/{id}/update-access', [DatabaseController::class, 'updateAccess'])
        ->name('databases.update-access');

    Route::get('/group-databases/create', [GroupDatabaseController::class, 'create'])
        ->name('group-databases.create');

    Route::post('/group-databases', [GroupDatabaseController::class, 'store'])
        ->name('group-databases.store');

    Route::delete('/group-databases/{id}', [GroupDatabaseController::class, 'destroy'])
        ->name('group-databases.destroy');
});

// =========================
// Google OAuth Authentication
// =========================

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {

    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
        ->name('user.dashboard');

    Route::put('/user/password', [UserDashboardController::class, 'updatePassword'])
        ->name('user.password.update');
});

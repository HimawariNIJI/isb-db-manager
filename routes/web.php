<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


// =========================
// Authentication
// =========================

Route::get('/login', [LoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');


// =========================
// Dashboard
// =========================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


// =========================
// Student Management
// =========================

Route::get('/students/create', function () {
    return view('students.create');
})->name('students.create');

Route::post('/students', [StudentController::class, 'store'])
    ->name('students.store');

Route::get('/students/import', function () {
    return view('students.import');
})->name('students.import');

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
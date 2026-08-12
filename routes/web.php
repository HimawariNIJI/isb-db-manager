<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

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

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


// =========================
// Student Management
// =========================

Route::get('/students/create', function () {
    return view('students.create');
})->name('students.create');

Route::get('/students/import', function () {
    return view('students.import');
})->name('students.import');

Route::get('/students', function () {
    return view('students.index');
})->name('students.index');
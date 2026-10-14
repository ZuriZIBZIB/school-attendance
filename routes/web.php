<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('/admin/dashboard', 'dashboard.placeholder', ['role' => 'Admin'])
        ->middleware('role:admin')->name('dashboard.admin');
    Route::resource('students', StudentController::class)->except('show');
    Route::view('/guru/dashboard', 'dashboard.placeholder', ['role' => 'Guru'])
        ->middleware('role:guru')->name('dashboard.guru');
    Route::view('/siswa/dashboard', 'dashboard.placeholder', ['role' => 'Siswa'])
        ->middleware('role:siswa')->name('dashboard.siswa');

    Route::middleware('role:admin')->group(function () {
        Route::resource('classrooms', ClassroomController::class)->except('show');
    });
});

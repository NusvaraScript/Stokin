<?php

use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Customer\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

// Auth Endpoints
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:api')
    ->name('logout');
Route::get('/user', [AuthController::class, 'user'])
    ->middleware('auth:api')
    ->name('user');

// Customer Endpoints
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers', [CustomerController::class, 'create'])->name('customers.create');
Route::put('/customers/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

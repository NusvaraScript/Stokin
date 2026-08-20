<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard')->name('dashboard');
Route::livewire('/customers', 'pages::customers.index')->name('customers.index');
Route::livewire('/transactions', 'pages::transactions.index')->name('transactions.index');
Route::livewire('/transactions/create', 'pages::transactions.create')->name('transactions.create');
Route::livewire('/transactions/{transaction}', 'pages::transactions.show')->name('transactions.show');
Route::livewire('/debt-payments', 'pages::debt-payments.index')->name('debt-payments.index');
Route::livewire('/expenses', 'pages::expenses.index')->name('expenses.index');

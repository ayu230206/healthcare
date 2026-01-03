<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\BillingAmountController;
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [DashboardController::class, 'index']);
Route::resource('patients', PatientController::class);
Route::get('/billing', [BillingAmountController::class, 'index'])->name('billing.index');
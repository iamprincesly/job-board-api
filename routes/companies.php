<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\CompanyAuthController;
use App\Http\Controllers\API\CompanyDashboardController;

Route::prefix('auth')->name('auth.')->controller(CompanyAuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
});

Route::middleware('auth:company-api')->group(function () {
    Route::get('dashboard', [CompanyDashboardController::class, 'dashboardData']);
    Route::apiResource('jobs', JobController::class);
});

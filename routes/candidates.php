<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CandidateAuthController;
use App\Http\Controllers\API\CandidateDashboardController;

Route::prefix('auth')->name('auth.')->controller(CandidateAuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
});

Route::middleware('auth:candidate-api')->group(function () {
    Route::get('job-applications', [CandidateDashboardController::class, 'fetchAppliedJobs']);
});

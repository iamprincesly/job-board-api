<?php

use App\Http\Controllers\API\PublicJobController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    // Company routes
    Route::prefix('companies')->name('companies.')->group(base_path('routes/companies.php'));

    // Candidates routes
    Route::prefix('candidates')->name('candidates.')->group(base_path('routes/candidates.php'));

    Route::prefix('jobs')->name('jobs.')->controller(PublicJobController::class)->group(function () {
        Route::get('/', 'listJobs')->name('job-list');
        Route::post('{job}/apply', 'applyForJob')->middleware('auth:candidate-api')->name('job-apply');
    });
});

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyDashboardController extends Controller
{
    /**
     * Fetch matric data for company dashbaord
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardData(Request $request): JsonResponse
    {
        $company = $request->user();

        $data = [
            'job_count' => $company->jobs()->count(),
            'application_count' => $company->jobs()->withCount('applications')->get()->sum('applications_count'),
        ];

        return api_success('Dashboard data fetched successfully.', $data);
    }
}

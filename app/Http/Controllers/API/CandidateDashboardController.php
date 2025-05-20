<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ApplicationRepository;
use App\Http\Resources\ApplicationCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CandidateDashboardController extends Controller
{
    public function __construct(private ApplicationRepository $applicationRepository)
    {}

    /**
     * Fetch job applications
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function fetchAppliedJobs(Request $request): ResourceCollection
    {
        $applications = $this->applicationRepository->getCandidateApplications($request, $request->user(), ['job']);
        return new ApplicationCollection($applications);
    }
}

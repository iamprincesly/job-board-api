<?php

namespace App\Http\Controllers\API;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\JobResource;
use App\Repositories\JobRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobCollection;
use App\Http\Requests\API\CreateJobRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JobController extends Controller
{
    public function __construct(private JobRepository $jobRepository)
    {}

    /**
     * fetch job list
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        $jobs = $this->jobRepository->companyJobs($request, $request->user());
        return new JobCollection($jobs);
    }

    /**
     * Create a new job
     *
     * @param \App\Http\Requests\API\CreateJobRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateJobRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['company_id'] = $request->user()->getKey();

        $job = DB::transaction(fn () => $this->jobRepository->create($data));

        return api_success('Job created successfully', new JobResource($job), 201);
    }

    /**
     * Show detail of a single job
     *
     * @param \Illuminate\Http\Request $request
     * @param string $job_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, string $job_id): JsonResponse
    {
        $job = Job::where('company_id', $request->user()->getKey())->where('id', $job_id)->first();

        if (!$job) {
            return api_failed('Job not found', 404);
        }
        return api_success('Job fetched successfully', new JobResource($job));
    }

    /**
     * Update a job
     *
     * @param \App\Http\Requests\API\CreateJobRequest $request
     * @param string $job_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateJobRequest $request, string $job_id): JsonResponse
    {
        $job = Job::where('company_id', $request->user()->getKey())->where('id', $job_id)->first();

        if (!$job) {
            return api_failed('Job not found', 404);
        }

        $job = DB::transaction(fn () => $this->jobRepository->make($job)->updateOne($request->validated()));

        return api_success('Job updated successfully', new JobResource($job));
    }

    /**
     * Soft delete a job
     *
     * @param \Illuminate\Http\Request $request
     * @param string $job_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $job_id): JsonResponse
    {
        $job = Job::where('company_id', $request->user()->getKey())->where('id', $job_id)->first();

        if (!$job) {
            return api_failed('Job not found', 404);
        }

        $job->delete();

        return api_success('Job deleted successfully');
    }
}

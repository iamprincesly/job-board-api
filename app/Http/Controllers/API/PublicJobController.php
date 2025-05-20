<?php

namespace App\Http\Controllers\API;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\JobRepository;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobCollection;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessApplicationDocuments;
use App\Http\Resources\ApplicationResource;
use App\Repositories\ApplicationRepository;
use App\Http\Requests\API\JobApplicationRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PublicJobController extends Controller
{
    public function __construct(private JobRepository $jobRepository, private ApplicationRepository $applicationRepository)
    {}

    /**
     * fetch job list
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function listJobs(Request $request): ResourceCollection
    {
        $jobs = Cache::remember('public_jobs_'.md5(serialize($request->all())), 300, fn() => $this->jobRepository->getAll($request));
        return new JobCollection($jobs);
    }

    /**
     * Candidate can apply for job
     *
     * @param \App\Http\Requests\API\JobApplicationRequest $request
     * @param \App\Models\Job $job
     *
     * @return void
     */
    public function applyForJob(JobApplicationRequest $request, Job $job)
    {
        $candidate = $request->user();

        if ($job->applications()->where('candidate_id', $candidate->getKey())->exists()) {
            return api_failed('You have already applied to this job');
        }

        $resumePath = $request->file('resume')->store('resumes');

        $coverLetterPath = $request->file('cover_letter')->store('cover_letters');

        $data = [
            'company_job_id' => $job->id,
            'candidate_id' => $candidate->getKey(),
            'cover_letter' => $request->cover_letter_text,
            'cover_letter_file' => $coverLetterPath,
            'resume_path' => $resumePath,
        ];

        $application = DB::transaction(fn () => $this->applicationRepository->create($data));

        // Queue document processing
        ProcessApplicationDocuments::dispatch($application);

        return api_success('Application submitted successfully.', new ApplicationResource($application), 201);
    }
}

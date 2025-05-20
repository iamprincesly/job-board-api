<?php

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessApplicationDocuments implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Application $application)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Processing documents for application ID: {$this->application->id}");
        Storage::move($this->path, 'processed/' . basename($this->path));
    }
}

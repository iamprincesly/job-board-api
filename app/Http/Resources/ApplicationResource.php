<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'cover_letter' => $this->cover_letter,
            'resume_path' => $this->resume_path,
            'cover_letter_file' => $this->cover_letter_file,
            'job' => $this->whenLoaded('job', fn () => new JobResource($this->job)),
            'candidate' => $this->whenLoaded('candidate', fn () => new CandidateResource($this->candidate)),
            'applied_at' => $this->created_at->getTimestamp(),
        ];
    }
}

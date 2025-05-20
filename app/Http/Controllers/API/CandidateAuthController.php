<?php

namespace App\Http\Controllers\API;

use App\Enums\AuthScope;
use App\Models\Candidate;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\CandidateResource;
use App\Repositories\CandidateRepository;
use App\Http\Requests\API\CandidateRegistrationRequest;

class CandidateAuthController extends Controller
{
    public function __construct(private CandidateRepository $candidateRepository, private AuthService $authService)
    {}

    public function login(LoginRequest $request): JsonResponse
    {
        $candidate = Candidate::where('email', $request->validated()['email'])->first();

        if (!$candidate || !Hash::check($request->password, $candidate->password)) {
            return api_failed('The provided credentials are incorrect.');
        }

        $token = $this->authService->createToken($candidate, AuthScope::CANDIDATE);

        return api_success('Logged in successfully.', ['candidate' => new CandidateResource($candidate), 'access_token' => $token]);
    }

    public function register(CandidateRegistrationRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $candidate = $this->candidateRepository->create($request->validated());

            $token = $this->authService->createToken($candidate, AuthScope::CANDIDATE);

            DB::commit();

            return api_success('Account created successfully.', ['candidate' => new CandidateResource($candidate), 'access_token' => $token]);
        } catch (\Throwable $th) {

            DB::rollBack();

            report($th);

            return api_failed('Could not create account, please try again later.');
        }
    }
}

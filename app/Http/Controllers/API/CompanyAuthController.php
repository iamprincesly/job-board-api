<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use App\Enums\AuthScope;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\API\LoginRequest;
use App\Http\Resources\CompanyResource;
use App\Repositories\CompanyRepository;
use App\Http\Requests\API\CompanyRegistrationRequest;

class CompanyAuthController extends Controller
{
    public function __construct(private CompanyRepository $companyRepository, private AuthService $authService)
    {}

    public function login(LoginRequest $request): JsonResponse
    {
        $company = Company::where('email', $request->validated()['email'])->first();

        if (!$company || !Hash::check($request->password, $company->password)) {
            return api_failed('The provided credentials are incorrect.');
        }

        $token = $this->authService->createToken($company, AuthScope::COMPANY);

        return api_success('Logged in successfully.', ['company' => new CompanyResource($company), 'access_token' => $token]);
    }

    public function register(CompanyRegistrationRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $company = $this->companyRepository->create($request->validated());

            $token = $this->authService->createToken($company, AuthScope::COMPANY);

            DB::commit();

            return api_success('Account created successfully.', ['company' => new CompanyResource($company), 'access_token' => $token]);
        } catch (\Throwable $th) {

            DB::rollBack();

            report($th);

            return api_failed('Could not create account, please try again later.');
        }
    }
}

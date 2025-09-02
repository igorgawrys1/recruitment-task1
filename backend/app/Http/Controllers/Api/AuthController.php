<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $patient = $this->patientRepository->findByCredentials(
            $request->input('login'),
            $request->input('password')
        );

        if (!$patient) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        $token = Auth::guard('api')->login($patient);

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}

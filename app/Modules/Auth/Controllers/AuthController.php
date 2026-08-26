<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller {
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'data' => $result,
        ], 200);
    }
}
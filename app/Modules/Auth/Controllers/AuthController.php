<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\AuthResource;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function user(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => new AuthResource($user),
        ]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());
        $data = [
            ...$data,
            'user' => new AuthResource($data['user']),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'data' => $data,
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());
        $data = [
            ...$data,
            'user' => new AuthResource($data['user']),
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil. Silakan login untuk melanjutkan',
            'data' => $data,
        ], 201);
    }

    public function logout(): JsonResponse
    {
        if (! $this->authService->logout()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada token yang dapat di hapus atau token tidak valid.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }
}

<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller {
    protected AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function user(): JsonResponse {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid.',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user,
        ]);
    }

    public function login(Request $request): JsonResponse {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($credentials);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil.',
            'data' => $result,
        ], 200);
    }

    public function register(Request $request): JsonResponse {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->authService->register($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil. Silakan login untuk melanjutkan',
            'data' => $result,
        ], 201);
    }

    public function logout(Request $request): JsonResponse {
        if (!$this->authService->logout()) {
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
<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $credentials): array
    {
        $user = $this->authRepository->findByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak valid.'],
            ]);
        }

        $scope = $user->role === 'admin' ? ['admin'] : ['user'];

        $token = $user->createToken('auth_token', $scope)->accessToken;

        return [
            'user' => $user,
            'token_type' => 'Bearer',
            'token' => $token,
            'role' => $user->role,
        ];
    }

    public function register(array $data): array
    {   
        $user = $this->authRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
        ]);

        $scope = $user->role === 'admin' ? ['admin'] : ['user'];

        $token = $user->createToken('auth_token', $scope)->accessToken;

        return [
            'user' => $user,
            'message' => 'Registrasi berhasil. Silakan login untuk melanjutkan',
            'token_type' => 'Bearer',
            'token' => $token,
            'role' => $user->role,
        ];
    }

    public function logout(): bool {
        $user = auth('api')->user();

        if (!$user || !$user->token()) {
            return false;
        }

        return $user->token()->revoke();
    }
}

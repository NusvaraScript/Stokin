<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Contracts\AuthRepositoryInterface;
use Illuminate\Supports\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService {
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository) {
        $this->authRepository = $authRepository;
    }
}
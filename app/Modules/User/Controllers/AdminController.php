<?php

namespace App\Modules\User\Controllers;

class AdminController
{
    public function index()
    {
        return response()->json(['message' => 'Admin Controller']);
    }
}
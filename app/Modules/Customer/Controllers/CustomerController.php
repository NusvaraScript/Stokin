<?php

namespace App\Modules\Customer\Controllers;

class CustomerController
{
    public function index()
    {
        return response()->json(['message' => 'Customer Controller']);
    }
}
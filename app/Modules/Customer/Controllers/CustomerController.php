<?php

namespace App\Modules\Customer\Controllers;

class CustomerController
{
    public function index()
    {
        return response()->json(['message' => 'Customer Controller']);
    }

    public function create()
    {
        return response()->json(['message' => 'Customer created']);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'Customer updated']);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Customer deleted']);
    }
}
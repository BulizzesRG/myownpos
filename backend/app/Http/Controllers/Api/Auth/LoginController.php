<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(!Auth::attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'is_active' => 1,
            'is_staff' => 1
        ])){

            return response()->json([
                "status" => "fail",
                "message" => __("auth.failed")
              ], 401);
        }

        return response()->json([
            "status" => "success",
            "data" => [
                "type" => 'token',
                "attributes" => [
                    "access_token" => Auth::user()->createToken('API Token')->plainTextToken,
                    "token_type" => 'Bearer'
                ]                    
            ]
        ])->withHeaders([
            'Location' => route('api.v1.home')
        ]);        
    }
}

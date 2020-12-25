<?php

namespace App\Http\Controllers;

use App\Libs\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        //if user is not found, will throw exception
        $user = User::findByEmailOrThrow($request->email);

        //if password is incorrect, will throw exception
        $user->checkPassword($request->password);

        $token = $user->createToken($request->device_name)->plainTextToken;

        return ApiResponse::make()->data([
            'user' => $user->toArray(),
            'token' => $token
        ])->json();
    }
}

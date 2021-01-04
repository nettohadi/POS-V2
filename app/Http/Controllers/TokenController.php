<?php

namespace App\Http\Controllers;

use App\Http\Requests\TokenRequest;
use App\Libs\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function store(TokenRequest $request)
    {
        //if user is not found, will throw exception
        $user = User::findByEmailOrThrow($request->email);

        $token = $user->getToken($request->password,$request->device_name);

        return ApiResponse::make()->isCreated([
            'user' => $user->toArray(),
            'token' => $token
        ])->json();
    }

    public function destroy()
    {
        request()->user()->currentAccessToken()->delete();
        return ApiResponse::make()->isDeleted()->json();
    }


}

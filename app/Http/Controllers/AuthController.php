<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\BusinessLogic\Abilities;
use App\Http\Requests\AuthUserRequest;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(AuthUserRequest $request)
    {
        $user = User::whereEmail($request->safe()['email'])->first();

        if (!$user)
            return response()->json(['message' => 'Email or password is incorrect'], HttpResponse::HTTP_BAD_REQUEST);

        if (Hash::check($request->safe()['password'], $user->password)) {
            Auth::login($user);
            $role = $user->role->name;
            $abilities = Abilities::getAbilitiesByRole($role);
            $token = Auth::user()->createToken($user->email, $abilities);
            return response()->json([
                'token' => $token->plainTextToken
            ], HttpResponse::HTTP_OK);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\BusinessLogic\Abilities;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        request()->validate([
            'email' => 'required|email',
            'password' => ['required', Password::min(8)->mixedCase()]
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            Auth::login($user);
            $role = $user->role->name;
            $abilities = Abilities::getAbilitiesByRole($role);
            $token = $request->user()->createToken($user->email, $abilities);
            return response()->json([
                'token' => $token->plainTextToken
            ]);
        }

        return response()->json([
            'message' => 'Incorrect email o password'
        ], Response::HTTP_BAD_REQUEST);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::user()->tokenCan('employee:manage')) {
            return response()->json([
                'message' => 'Insufficient Permissions'
            ], Response::HTTP_FORBIDDEN);
        }

        request()->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => ['required', Password::min(8)->mixedCase()]
        ]);

        $user = User::where('email', request('email'))->first();

        if ($user)
            return response()->json([
                'message' => "The email {$request->input('email')} is alreade in use"
            ], Response::HTTP_BAD_REQUEST);

        $role = Role::where('name', Roles::EMPLOYEE)->first();
        $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password'),
            'role_id' => $role->id
        ]);

        return response()->noContent(Response::HTTP_CREATED, ['resource' => route('employee', [$user->id])]);
    }

    public function show()
    {
        // code here
    }
}

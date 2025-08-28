<?php

namespace App\BusinessLogic\services\implementation;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Response;
use App\BusinessLogic\services\definition\IUserService;

class UserService implements IUserService
{
    public function create($user_from_req, $role_name)
    {
        $user = User::whereEmail(request('email'))->first();

        if ($user)
            return response()->json([
                'message' => "The email {$user_from_req['email']} is already in use"
            ], Response::HTTP_BAD_REQUEST);

        $role = Role::where('name', $role_name)->first();

        $new_user = User::create([
            'name' => $user_from_req['name'],
            'email' => $user_from_req['email'],
            'password' => $user_from_req['password'],
            'role_id' => $role->id
        ]);

        return response()->noContent(Response::HTTP_CREATED, ['resource' => route('user', [$new_user->id])]);
    }
}

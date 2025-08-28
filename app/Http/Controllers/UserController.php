<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\BusinessLogic\services\definition\IUserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Str;

class UserController extends Controller
{
    public function __construct(private IUserService $userService) {}

    public function index()
    {
        if (!Auth::user()->tokenCan('user:list')) {
            return response()->json([
                'message' => 'Insufficient Permissions'
            ], Response::HTTP_FORBIDDEN);
        }

        return UserResource::collection(User::with('role')->get());
    }

    public function store(StoreUserRequest $request)
    {
        $route = request()->uri()->path();

        $role = Str::contains($route, 'employee')
            ? Roles::EMPLOYEE->value : Roles::CLIENT->value;

        return $this->userService->create($request->safe(), $role);
    }

    public function show(User $user)
    {
        if (Auth::id() !== $user->id && !Auth::user()->tokenCan('user:list')) {
            return response()->json([
                'message' => 'You\'re trying to retrieve information not related to you or you have insufficent permissons'
            ], Response::HTTP_FORBIDDEN);
        }

        $user->load('role');

        return new UserResource($user);
    }

    public function update(User $user)
    {
        if (!Auth::user()->tokenCan('user:update')) {
            return response()->json([
                'message' => 'Insufficient Permissions'
            ], Response::HTTP_FORBIDDEN);
        }

        $user->update([
            'name' => request('name')
        ]);

        return response()->noContent(204, ['resource' => route('user', [$user->id])]);
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->tokenCan('user:delete')) {
            return response()->json([
                'message' => 'Insufficient Permissions'
            ], Response::HTTP_FORBIDDEN);
        }

        $user->delete();

        return response()->noContent();
    }
}

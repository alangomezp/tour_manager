<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\User;
use App\BusinessLogic\services\definition\IUserService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
use Str;

class UserController extends Controller
{
    public function __construct(private IUserService $userService) {}

    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection(User::with('role')->get());
    }

    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $route = request()->uri()->path();

        $role = Str::contains($route, 'employee')
            ? Roles::EMPLOYEE->value : Roles::CLIENT->value;

        return $this->userService->create($request->safe(), $role);
    }

    public function show(User $user)
    {
        Gate::authorize('view', [User::class, $user]);

        $user->load('role');

        return new UserResource($user);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        Gate::authorize('update', User::class);

        $user->update($request->safe()->toArray());

        return response()->noContent(204, ['resource' => route('user', [$user->id])]);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', User::class);

        $user->delete();

        return response()->noContent();
    }
}

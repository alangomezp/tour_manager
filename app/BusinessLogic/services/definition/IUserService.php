<?php

namespace App\BusinessLogic\services\definition;

interface IUserService
{
    public function create(array $user, string $role);
}

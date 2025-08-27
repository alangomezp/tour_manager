<?php

namespace App\BusinessLogic;

use App\Enums\Roles;

class Abilities
{
    public static function getAbilitiesByRole(string $role)
    {
        return match ($role) {
            Roles::ADMIN->value => ['*'],
            Roles::EMPLOYEE->value => ['rsvp:manage'],
            Roles::CLIENT->value => ['tour:view', 'rsvp:create'],
            default => []
        };
    }
}

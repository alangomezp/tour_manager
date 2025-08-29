<?php

namespace App\BusinessLogic;

use App\Enums\Roles;

class Abilities
{
    public static function getAbilitiesByRole(string $role)
    {
        return match ($role) {
            Roles::ADMIN->value => [
                'user:create',
                'user:list',
                'user:update',
                'user:delete',
                'tour:list',
                'tour:create',
                'tour:update',
                'tour:delete'
            ],
            Roles::EMPLOYEE->value => ['tour:list', 'rsvp:manage'],
            Roles::CLIENT->value => ['tour:list', 'rsvp:create'],
            default => []
        };
    }
}

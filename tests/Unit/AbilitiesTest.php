<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\TestCase;
use App\BusinessLogic\Abilities;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AbilitiesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function if_user_has_admin_role_then_get_all_abilities()
    {
        //Arrange
        $role = 'admin';

        //Act
        $abilities = Abilities::getAbilitiesByRole($role);
        //Assert
        $this->assertNotNull($abilities);
        $this->assertEquals([
            'user:create',
            'user:list',
            'user:update',
            'user:delete'
        ], $abilities);
    }

    #[Test]
    public function if_user_has_employee_role_then_get_rsvp_manage_abilities()
    {
        //Arrange
        $role = 'employee';

        //Act
        $abilities = Abilities::getAbilitiesByRole($role);
        //Assert
        $this->assertNotNull($abilities);
        $this->assertEquals(['rsvp:manage'], $abilities);
    }

    #[Test]
    public function if_user_has_client_role_then_get_tour_view_and_rsvp_create_abilities()
    {
        //Arrange
        $role = 'client';

        //Act
        $abilities = Abilities::getAbilitiesByRole($role);
        //Assert
        $this->assertNotNull($abilities);
        $this->assertEquals(['tour:view', 'rsvp:create'], $abilities);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\Roles;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegisterEmployeeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function as_an_admin_can_register_new_employee_user()
    {
        // Arrange
        $role_admin = Role::factory()->create([
            'name' => Roles::ADMIN->value
        ]);

        Role::factory()->create([
            'name' => Roles::EMPLOYEE->value
        ]);

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Password',
            'role_id' => $role_admin->id
        ]);

        Sanctum::actingAs($admin, ['user:create']);

        $employee = [
            'name' => 'perez',
            'email' => 'perez@exampletour.com',
            'password' => 'Password',
        ];

        // Act
        $response = $this->post('api/employee', $employee);
        $user = User::where('email', 'perez@exampletour.com')->with('role')->first();
        // Assert
        $response->assertStatus(201)
            ->assertHeader('resource', route('user', [$user->id]));
        $this->assertNotNull($user->role_id);
        $this->assertEquals('employee', $user->role->name);
    }

    #[Test]
    public function as_an_employee_cant_register_new_employee_user()
    {
        // Arrange
        $role = Role::factory()->create([
            'name' => Roles::EMPLOYEE->value
        ]);

        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Password',
            'role_id' => $role->id
        ]);

        Sanctum::actingAs($admin); //no permissons

        $employee = [
            'name' => 'perez',
            'email' => 'perez@exampletour.com',
            'password' => 'Password',
        ];

        // Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/employee', $employee);

        // Assert
        $response->assertStatus(403);
    }

    #[Test]
    public function password_should_contain_min_8_characters_and_1_uppercase_and_1_lowercase()
    {
        //Arrange
        $admin = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Password',
            'role_id' => null
        ]);

        Sanctum::actingAs($admin, ['*']);

        $employee = [
            'name' => 'perez',
            'email' => 'perez@exampletour.com',
            'password' => '1234',
        ];

        // Act
        $response = $this->post('api/employee', $employee);

        // Assert
        $response->assertSessionHasErrors();
    }
}

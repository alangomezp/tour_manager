<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function as_admin_user_with_valid_credentials_can_login_and_get_admin_abilities()
    {
        //Arrange
        $role = Role::factory()->create([
            'name' => 'admin'
        ]);

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password',
            'role_id' => $role->id
        ]);

        // Act & Assert
        $response = $this->post('api/login', [
            'email' => 'test@example.com',
            'password' => 'Password'
        ]);

        $response->assertOk()
            ->assertJson(['token' => $response->json()['token']]);

        $user = User::where('email', 'test@example.com')->with('role')->first();
        $this->assertNotNull($user);
        $this->assertEquals('admin', $user->role->name);

        $token = PersonalAccessToken::where('tokenable_id', $user->id)->first();
        $this->assertNotNull($token);
        $this->assertEquals($token->abilities, ['*']);
    }

    #[Test]
    public function employee_user_with_valid_credentials_can_login_and_get_employee_abilities()
    {
        //Arrange
        $role = Role::factory()->create([
            'name' => 'employee'
        ]);

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password',
            'role_id' => $role->id
        ]);

        // Act & Assert
        $response = $this->post('api/login', [
            'email' => 'test@example.com',
            'password' => 'Password'
        ]);

        $response->assertOk()
            ->assertJson(['token' => $response->json()['token']]);

        $user = User::where('email', 'test@example.com')->with('role')->first();
        $this->assertNotNull($user);
        $this->assertEquals('employee', $user->role->name);

        $token = PersonalAccessToken::where('tokenable_id', $user->id)->first();
        $this->assertNotNull($token);
        $this->assertEquals($token->abilities, ['rsvp:manage']);
    }

    #[Test]
    public function client_user_with_valid_credentials_can_login_and_get_client_abilities()
    {
        //Arrange
        $role = Role::factory()->create([
            'name' => 'client'
        ]);

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'Password',
            'role_id' => $role->id
        ]);

        // Act & Assert
        $response = $this->post('api/login', [
            'email' => 'test@example.com',
            'password' => 'Password'
        ]);

        $response->assertOk()
            ->assertJson(['token' => $response->json()['token']]);

        $user = User::where('email', 'test@example.com')->with('role')->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role->name);

        $token = PersonalAccessToken::where('tokenable_id', $user->id)->first();
        $this->assertNotNull($token);
        $this->assertEquals($token->abilities, ['tour:view', 'rsvp:create']);
    }

    #[Test]
    public function user_with_incorrect_credentials_cant_login()
    {
        //Arrange
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $response = $this->post('api/login', [
            'email' => 'invalid@example.com',
            'password' => 'Password'
        ]);

        // Assert
        $response->assertBadRequest()
            ->assertJson(['message' => 'Incorrect email o password']);
    }

    #[Test]
    public function user_with_invalid_email_cant_login()
    {
        //Arrange
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Act
        $response = $this->post('api/login', [
            'email' => null,
            'password' => 'Password'
        ]);

        // Assert
        $response->assertSessionHasErrors();
    }
}

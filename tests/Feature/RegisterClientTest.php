<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class RegisterClientTest extends TestCase
{
    use RefreshDatabase;

    protected $client_request;

    protected function setUp(): void
    {
        $this->client_request = [
            'name' => 'jose',
            'email' => 'jose@example.com',
            'password' => 'Password'
        ];

        parent::setUp();
    }

    #[Test]
    public function as_a_client_i_can_register_using_a_public_endpoint()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);
        //Act
        $response = $this->post('api/client', $this->client_request);
        $user = User::where('email', $this->client_request['email'])->with('role')->first();

        // Assert
        $response->assertStatus(201)
            ->assertHeader('resource', route('user', [$user->id]));
        $this->assertNotNull($user->role_id);
        $this->assertEquals('client', $user->role->name);
    }

    #[Test]
    public function as_a_client_i_try_to_register_using_a_password_no_cotain_min_8_length_and_atleaste_1_uppercase_and_1_lowercase()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);

        $this->client_request['password'] = '1234';
        //Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/client', $this->client_request);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function as_a_client_i_try_to_register_with_null_password()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);
        $this->client_request['password'] = null;

        //Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/client', $this->client_request);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function as_a_client_i_try_to_register_with_in_use_email()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);
        User::factory()->create($this->client_request);

        //Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/client', $this->client_request);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function as_a_client_i_try_to_register_with_invalid_email()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);
        $this->client_request['email'] = 'jose.gmail.com';

        //Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/client', $this->client_request);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function as_a_client_i_try_to_register_with_null_email()
    {
        //Arrange
        Role::factory()->create([
            'name' => 'client'
        ]);
        $this->client_request['email'] = null;

        //Act
        $response = $this->withHeader('Accept', 'application/json')
            ->post('api/client', $this->client_request);

        // Assert
        $response->assertStatus(422);
    }
}

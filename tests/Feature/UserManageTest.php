<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Exception;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserManageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {

        parent::setUp();
    }

    //Admin tests

    #[Test]
    public function as_admin_i_can_list_all_users()
    {
        //Arrange
        $admin = User::factory()->create();
        $role = Role::factory()->create([
            'name' => 'admin'
        ]);
        User::factory(4)->create([
            'role_id' => $role->id
        ]);
        Sanctum::actingAs($admin, ['user:list']);

        //Act
        $response = $this->getJson('api/user');

        //Assert
        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has(5)
                    ->each(fn($user) =>
                    $user->hasAll(['name', 'email', 'role']))
            );
    }

    #[Test]
    public function as_an_admin_i_can_retrieve_data_related_to_another_users()
    {
        //Arrange
        $admin = User::factory()->create([], Role::factory()->create());
        $another_user = User::factory()->create();
        Sanctum::actingAs($admin, ['user:list']);

        //Act
        $response = $this->getJson("api/user/{$another_user->id}");

        //Assert
        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['name', 'email', 'role'])
            );
    }

    #[Test]
    public function as_an_admin_i_can_update_an_user_information()
    {
        //Arrange
        $admin = User::factory()->create();
        $outdated_user = User::factory()->create();
        Sanctum::actingAs($admin, ['user:update']);

        //Act
        $response = $this->patchJson("api/user/{$outdated_user->id}", [
            'name' => 'Alan',
            'password' => 'Password'
        ]);
        $updated_user = User::find($outdated_user->id);

        //Assert
        $response->assertStatus(204)
            ->assertHeader('resource', route('user', [$updated_user->id]));
        $this->assertEquals('Alan', $updated_user->name);
    }

    #[Test]
    public function as_an_admin_i_can_delete_an_user()
    {
        //Arrange
        $admin = User::factory()->create();
        $another_user = User::factory()->create();
        Sanctum::actingAs($admin, ['user:delete']);

        //Act
        $response = $this->deleteJson("api/user/{$another_user->id}");
        $user_deleted = User::withTrashed()->find($another_user->id);

        //Assert
        $response->assertStatus(204);
        $this->assertTrue($user_deleted->trashed());
    }

    //Other Users

    #[Test]
    public function as_normal_user_i_cant_list_all_users()
    {
        //Arrange
        $user = User::factory()->create();
        $role = Role::factory()->create([
            'name' => 'client'
        ]);
        User::factory(4)->create([
            'role_id' => $role->id
        ]);
        Sanctum::actingAs($user);

        //Act
        $response = $this->getJson('api/user');

        //Assert
        $response->assertStatus(403);
    }

    #[Test]
    public function as_an_user_i_can_retrieve_my_personal_data()
    {
        //Arrange
        $user = User::factory()->create([], Role::factory()->create());
        Sanctum::actingAs($user);

        //Act
        $response = $this->getJson("api/user/{$user->id}");

        //Assert
        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['name', 'email', 'role'])
            );
    }

    #[Test]
    public function as_an_user_i_cant_retrieve_data_related_to_another_user()
    {
        //Arrange
        $user = User::factory()->create([], Role::factory()->create());
        $another_user = User::factory()->create();
        Sanctum::actingAs($user);

        //Act
        $response = $this->getJson("api/user/{$another_user->id}");

        //Assert
        $response->assertStatus(403);
        $this->assertTrue($response['message'] === 'This action is unauthorized.');
    }
}

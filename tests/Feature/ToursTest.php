<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Tour;
use App\Models\User;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class ToursTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {

        parent::setUp();
    }

    #[Test]
    public function all_users_with_tour_list_ability_can_list_tours()
    {
        // Arrange
        $user = User::factory()->create();
        Tour::factory(5)->create();
        Sanctum::actingAs($user, ['tour:list']);

        // Act
        $response = $this->getJson('api/tour');

        // Assert
        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->has(5)
                    ->each(
                        fn($tour) =>
                        $tour->hasAll(['title', 'description', 'price', 'available_spaces', 'total_spaces', 'date'])
                    )
            );
    }

    #[Test]
    public function all_users_with_tour_list_ability_can_find_by_id_any_tour()
    {
        // Arrange
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        Sanctum::actingAs($user, ['tour:list']);

        // Act
        $response = $this->getJson("api/tour/{$tour->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->hasAll(['title', 'description', 'price', 'available_spaces', 'total_spaces', 'date'])
            );
    }

    #[Test]
    public function as_an_admin_i_can_create_a_new_tour()
    {
        // Arrange
        $admin = User::factory()->create();
        $tour = Tour::factory()->make()->toArray();
        Sanctum::actingAs($admin, ['tour:create']);

        // Act
        $response = $this->postJson('api/tour', $tour);
        $tour_created = Tour::where('title', $tour['title'])->first();

        // Assert
        $this->assertNotNull($tour_created);
        $response->assertStatus(201)
            ->assertHeader('resource', route('tour', $tour_created->id));
    }

    #[Test]
    public function as_an_admin_i_can_update_a_tour()
    {
        // Arrange
        $admin = User::factory()->create();
        $tour = Tour::factory()->create();
        Sanctum::actingAs($admin, ['tour:update']);

        $tour_update = $tour->toArray();
        $tour_update['title'] = 'Viaje a Tajumulco';

        // Act
        $response = $this->patchJson("api/tour/{$tour->id}", $tour_update);
        $tour_updated = Tour::where('title', $tour_update['title'])->first();

        // Assert
        $response->assertStatus(204)
            ->assertHeader('resource', route('tour', $tour->id));
        $this->assertNotEquals($tour->title, $tour_updated->title);
    }

    #[Test]
    public function as_an_admin_i_can_delete_a_tour()
    {
        // Arrange
        $admin = User::factory()->create();
        $tour = Tour::factory()->create();
        Sanctum::actingAs($admin, ['tour:delete']);

        // Act
        $response = $this->deleteJson("api/tour/{$tour->id}");
        $tour_deleted = Tour::withTrashed()->find($tour->id);

        // Assert
        $response->assertStatus(204);
        $this->assertNotNull($tour_deleted->deleted_at);
    }

    #[Test]
    public function as_an_user_different_to_admin_i_cant_delete_update_or_create_a_tour()
    {
        // Arrange
        $user = User::factory()->create();
        $tour = Tour::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response_post = $this->postJson("api/tour", $tour->toArray());
        $response_patch = $this->patchJson("api/tour/{$tour->id}", $tour->toArray());
        $response_delete = $this->deleteJson("api/tour/{$tour->id}");

        // Assert
        $response_post->assertStatus(403);
        $response_patch->assertStatus(403);
        $response_delete->assertStatus(403);
    }
}

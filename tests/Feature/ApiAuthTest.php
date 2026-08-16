<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token', 'user' => ['name', 'email']]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_user_cannot_login_with_wrong_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noexiste@example.com',
            'password' => 'incorrecta',
        ]);

        $response->assertStatus(401);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/categories')->assertStatus(401);
    }

    public function test_token_grants_access_to_resources(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/categories')
            ->assertOk();
    }

    public function test_full_category_crud_via_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $created = $this->withToken($token)
            ->postJson('/api/categories', [
                'name' => 'Electronica',
                'description' => 'Productos electronicos',
                'is_active' => true,
            ])
            ->assertStatus(201)
            ->assertJson(['name' => 'Electronica']);

        $id = $created->json('id');

        $this->withToken($token)->getJson("/api/categories/{$id}")->assertOk();

        $this->withToken($token)
            ->putJson("/api/categories/{$id}", [
                'name' => 'Electronicos',
                'description' => 'Actualizada',
                'is_active' => true,
            ])
            ->assertOk()
            ->assertJson(['name' => 'Electronicos']);

        $this->withToken($token)->deleteJson("/api/categories/{$id}")->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $id]);
    }
}
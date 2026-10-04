<?php

use App\Models\Kid;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('api returns unauthorized without api key', function () {
    $response = $this->getJson('/api/players');

    $response->assertUnauthorized();
});

test('api returns unauthorized with invalid api key', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->getJson('/api/players');

    $response->assertUnauthorized();
});

test('api can list players with valid api key', function () {
    $user = User::factory()->create();
    $kids = Kid::factory()->count(2)->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/players');

    $response->assertOk();
    $response->assertJsonCount(2);
    $response->assertJsonStructure([
        '*' => ['id', 'name', 'user_id', 'created_at', 'updated_at'],
    ]);
});

test('api only returns players belonging to the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Kid::factory()->count(2)->create(['user_id' => $user->id]);
    Kid::factory()->count(1)->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/players');

    $response->assertOk();
    $response->assertJsonCount(2);
});

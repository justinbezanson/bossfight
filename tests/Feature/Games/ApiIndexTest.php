<?php

use App\Models\Game;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('api returns unauthorized without api key', function () {
    $response = $this->getJson('/api/games');

    $response->assertUnauthorized();
});

test('api returns unauthorized with invalid api key', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
        ->getJson('/api/games');

    $response->assertUnauthorized();
});

test('api can list games with valid api key', function () {
    $user = User::factory()->create();
    $games = Game::factory()->count(2)->create(['user_id' => $user->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/games');

    $response->assertOk();
    $response->assertJsonCount(2);
    $response->assertJsonStructure([
        '*' => ['id', 'name', 'processes', 'user_id', 'created_at', 'updated_at'],
    ]);
});

test('api only returns games belonging to the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Game::factory()->count(2)->create(['user_id' => $user->id]);
    Game::factory()->count(1)->create(['user_id' => $otherUser->id]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/games');

    $response->assertOk();
    $response->assertJsonCount(2);
});

test('api returns processes as an array', function () {
    $user = User::factory()->create();
    Game::factory()->create([
        'user_id' => $user->id,
        'processes' => ['explorer.exe', 'notepad.exe'],
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/games');

    $response->assertOk();
    $response->assertJsonFragment([
        'processes' => ['explorer.exe', 'notepad.exe'],
    ]);
});

<?php

use App\Models\Kid;
use App\Models\User;

beforeEach(function () {
    config()->set('app.api_key', 'test-api-key');
});

test('api returns unauthorized without api key', function () {
    $response = $this->postJson('/api/log/create', [
        'kid_id' => 1,
        'message' => 'Test message',
    ]);

    $response->assertUnauthorized();
});

test('api returns unauthorized with invalid api key', function () {
    $response = $this->withHeader('X-API-Key', 'wrong-key')
        ->postJson('/api/log/create', [
            'kid_id' => 1,
            'message' => 'Test message',
        ]);

    $response->assertUnauthorized();
});

test('api returns unauthorized when no users exist', function () {
    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', [
            'kid_id' => 1,
            'message' => 'Test message',
        ]);

    $response->assertUnauthorized();
});

test('api can create a log with valid api key', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);

    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', [
            'kid_id' => $kid->id,
            'message' => 'Test log message',
        ]);

    $response->assertCreated();
    $response->assertJson([
        'kid_id' => $kid->id,
        'message' => 'Test log message',
        'user_id' => $user->id,
    ]);
});

test('api validates required fields when creating a log', function () {
    User::factory()->create();

    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', []);

    $response->assertJsonValidationErrors(['kid_id', 'message']);
});

test('api validates kid exists when creating a log', function () {
    User::factory()->create();

    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', [
            'kid_id' => 999,
            'message' => 'Test message',
        ]);

    $response->assertJsonValidationErrors(['kid_id']);
});

test('api validates message length when creating a log', function () {
    User::factory()->create();

    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', [
            'kid_id' => 1,
            'message' => str_repeat('a', 513),
        ]);

    $response->assertJsonValidationErrors(['message']);
});

test('api returns forbidden when kid belongs to another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->withHeader('X-API-Key', 'test-api-key')
        ->postJson('/api/log/create', [
            'kid_id' => $kid->id,
            'message' => 'Test message',
        ]);

    $response->assertForbidden();
});

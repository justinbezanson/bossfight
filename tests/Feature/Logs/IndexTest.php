<?php

use App\Models\Log;
use App\Models\User;

test('guests are redirected from logs to the login page', function () {
    $response = $this->get(route('logs.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view their logs', function () {
    $user = User::factory()->create();
    Log::factory()->count(2)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('logs.index'));

    $response->assertOk();
});

test('users only see their own logs', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Log::factory()->count(2)->create(['user_id' => $user->id]);
    Log::factory()->count(1)->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('logs.index'));

    $response->assertOk();
});

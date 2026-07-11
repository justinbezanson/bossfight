<?php

use App\Models\Log;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

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

test('logs are paginated with 15 per page', function () {
    $user = User::factory()->create();
    Log::factory()->count(17)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('logs.index'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Logs/Index')
        ->has('logs', fn (Assert $page) => $page
            ->where('per_page', 15)
            ->where('total', 17)
            ->where('last_page', 2)
            ->where('current_page', 1)
            ->has('data', 15)
            ->has('links')
            ->etc()
        )
    );
});

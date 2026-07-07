<?php

use App\Models\Kid;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('kids.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view their kids', function () {
    $user = User::factory()->create();
    $kids = Kid::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('kids.index'));

    $response->assertOk();
});

test('users only see their own kids', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Kid::factory()->count(2)->create(['user_id' => $user->id]);
    Kid::factory()->count(1)->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('kids.index'));

    $response->assertOk();
});

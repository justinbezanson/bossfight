<?php

use App\Models\Kid;
use App\Models\User;

test('guests cannot update a kid', function () {
    $kid = Kid::factory()->create();

    $response = $this->put(route('kids.update', $kid), [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect(route('login'));
});

test('authenticated users can update their own kid', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('kids.update', $kid), [
        'name' => 'Updated Name',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('kids.index'));

    expect($kid->fresh()->name)->toBe('Updated Name');
});

test('users cannot update another users kid', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $otherUser->id, 'name' => 'Original Name']);

    $response = $this->actingAs($user)->put(route('kids.update', $kid), [
        'name' => 'Hacked Name',
    ]);

    $response->assertForbidden();

    expect($kid->fresh()->name)->toBe('Original Name');
});

test('name is required to update a kid', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('kids.update', $kid), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('name must not exceed 32 characters to update a kid', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put(route('kids.update', $kid), [
        'name' => str_repeat('a', 33),
    ]);

    $response->assertSessionHasErrors('name');
});

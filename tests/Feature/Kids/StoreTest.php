<?php

use App\Models\User;

test('guests cannot create a kid', function () {
    $response = $this->post(route('kids.store'), [
        'name' => 'Test Kid',
    ]);

    $response->assertRedirect(route('login'));
});

test('authenticated users can create a kid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('kids.store'), [
        'name' => 'Test Kid',
    ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('kids.index'));

    expect($user->kids()->count())->toBe(1);
    expect($user->kids()->first()->name)->toBe('Test Kid');
});

test('name is required to create a kid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('kids.store'), [
        'name' => '',
    ]);

    $response->assertSessionHasErrors('name');
});

test('name must be a string to create a kid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('kids.store'), [
        'name' => 123,
    ]);

    $response->assertSessionHasErrors('name');
});

test('name must not exceed 32 characters to create a kid', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('kids.store'), [
        'name' => str_repeat('a', 33),
    ]);

    $response->assertSessionHasErrors('name');
});

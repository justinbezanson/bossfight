<?php

use App\Models\User;

test('guests are redirected from appearance to the login page', function () {
    $response = $this->get(route('appearance.edit'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view the appearance page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('appearance.edit'));

    $response->assertOk();
});

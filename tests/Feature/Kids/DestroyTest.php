<?php

use App\Models\Kid;
use App\Models\User;

test('guests cannot delete a kid', function () {
    $kid = Kid::factory()->create();

    $response = $this->delete(route('kids.destroy', $kid));

    $response->assertRedirect(route('login'));
});

test('authenticated users can delete their own kid', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete(route('kids.destroy', $kid));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('kids.index'));

    expect(Kid::find($kid->id))->toBeNull();
});

test('users cannot delete another users kid', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->delete(route('kids.destroy', $kid));

    $response->assertForbidden();

    expect(Kid::find($kid->id))->not->toBeNull();
});

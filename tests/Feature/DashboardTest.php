<?php

use App\Models\Game;
use App\Models\Kid;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard returns top games and recent sessions', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create(['user_id' => $user->id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'kid_id' => $kid->id,
        'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('topGames', 1)
        ->has('recentSessions', 1)
    );
});

test('sessions are split when gap exceeds 5 minutes', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);
    $game = Game::factory()->create(['user_id' => $user->id]);

    Log::factory()->create([
        'user_id' => $user->id,
        'kid_id' => $kid->id,
        'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id,
        'kid_id' => $kid->id,
        'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:03:00'),
    ]);
    // Gap > 6 minutes — new session
    Log::factory()->create([
        'user_id' => $user->id,
        'kid_id' => $kid->id,
        'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:10:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id,
        'kid_id' => $kid->id,
        'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:12:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('topGames', 1, fn (Assert $page) => $page
            ->where('session_count', 2)
            ->etc()
        )
        ->has('recentSessions', 2)
    );
});

test('top games are ordered by total play time', function () {
    $user = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);
    $game1 = Game::factory()->create(['user_id' => $user->id, 'name' => 'Minecraft']);
    $game2 = Game::factory()->create(['user_id' => $user->id, 'name' => 'Fortnite']);
    $game3 = Game::factory()->create(['user_id' => $user->id, 'name' => 'Roblox']);
    $game4 = Game::factory()->create(['user_id' => $user->id, 'name' => 'Mario']);

    // Minecraft: 2 sessions — one 4 min, one 3 min = 7 min total
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game1->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game1->id,
        'log_date' => Carbon::parse('2026-07-10 10:04:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game1->id,
        'log_date' => Carbon::parse('2026-07-10 10:12:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game1->id,
        'log_date' => Carbon::parse('2026-07-10 10:15:00'),
    ]);

    // Fortnite: 1 session of 5 min
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game2->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game2->id,
        'log_date' => Carbon::parse('2026-07-10 10:05:00'),
    ]);

    // Roblox: 1 session of 2 min
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game3->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game3->id,
        'log_date' => Carbon::parse('2026-07-10 10:02:00'),
    ]);

    // Mario: 1 session of 1 min
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game4->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game4->id,
        'log_date' => Carbon::parse('2026-07-10 10:01:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('topGames', 3, fn (Assert $page) => $page
            ->where('game.name', 'Minecraft')
            ->where('total_minutes', 7)
            ->where('session_count', 2)
            ->etc()
        )
    );
});

test('users only see their own data', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $kid = Kid::factory()->create(['user_id' => $user->id]);
    $otherKid = Kid::factory()->create(['user_id' => $otherUser->id]);
    $game = Game::factory()->create(['user_id' => $user->id]);
    $otherGame = Game::factory()->create(['user_id' => $otherUser->id]);

    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:03:00'),
    ]);

    Log::factory()->create([
        'user_id' => $otherUser->id, 'kid_id' => $otherKid->id, 'game_id' => $otherGame->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $otherUser->id, 'kid_id' => $otherKid->id, 'game_id' => $otherGame->id,
        'log_date' => Carbon::parse('2026-07-10 10:03:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('topGames', 1, fn (Assert $page) => $page
            ->where('game.name', $game->name)
            ->etc()
        )
        ->has('recentSessions', 1, fn (Assert $page) => $page
            ->where('kid.name', $kid->name)
            ->etc()
        )
    );
});

test('recent sessions include kid information', function () {
    $user = User::factory()->create();
    $kid1 = Kid::factory()->create(['user_id' => $user->id, 'name' => 'Alice']);
    $kid2 = Kid::factory()->create(['user_id' => $user->id, 'name' => 'Bob']);
    $game = Game::factory()->create(['user_id' => $user->id]);

    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid1->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid1->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 10:03:00'),
    ]);

    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid2->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 11:00:00'),
    ]);
    Log::factory()->create([
        'user_id' => $user->id, 'kid_id' => $kid2->id, 'game_id' => $game->id,
        'log_date' => Carbon::parse('2026-07-10 11:03:00'),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->has('recentSessions', 2, fn (Assert $page) => $page
            ->where('kid.name', 'Bob')
            ->etc()
        )
    );
});

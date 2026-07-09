<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'processes' => fake()->randomElements(['explorer.exe', 'notepad.exe', 'calc.exe', 'cmd.exe', 'chrome.exe'], fake()->numberBetween(1, 3)),
            'user_id' => User::factory(),
        ];
    }
}

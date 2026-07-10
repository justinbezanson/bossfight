<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Kid;
use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Log>
 */
class LogFactory extends Factory
{
    protected $model = Log::class;

    public function definition(): array
    {
        return [
            'log_date' => fake()->dateTimeThisMonth(),
            'kid_id' => Kid::factory(),
            'game_id' => Game::factory(),
            'user_id' => User::factory(),
            'message' => fake()->sentence(),
        ];
    }
}

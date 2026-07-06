<?php

namespace Database\Factories;

use App\Models\Kid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kid>
 */
class KidFactory extends Factory
{
    protected $model = Kid::class;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'user_id' => User::factory(),
        ];
    }
}

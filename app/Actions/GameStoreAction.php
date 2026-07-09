<?php

namespace App\Actions;

use App\Http\Requests\StoreGameRequest;
use App\Models\Game;
use App\Models\User;

class GameStoreAction
{
    public function execute(StoreGameRequest $request, User $user): Game
    {
        return Game::create([
            'name' => $request->input('name'),
            'processes' => $request->input('processes'),
            'user_id' => $user->id,
        ]);
    }
}

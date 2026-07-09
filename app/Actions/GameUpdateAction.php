<?php

namespace App\Actions;

use App\Http\Requests\UpdateGameRequest;
use App\Models\Game;

class GameUpdateAction
{
    public function execute(UpdateGameRequest $request, Game $game): Game
    {
        $game->update([
            'name' => $request->input('name'),
            'processes' => $request->input('processes'),
        ]);

        return $game;
    }
}

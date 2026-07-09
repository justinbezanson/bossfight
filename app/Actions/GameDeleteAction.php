<?php

namespace App\Actions;

use App\Models\Game;

class GameDeleteAction
{
    public function execute(Game $game): void
    {
        $game->delete();
    }
}

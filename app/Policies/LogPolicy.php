<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\Kid;
use App\Models\Log;
use App\Models\User;

class LogPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Log $log): bool
    {
        return $log->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Check that the log's kid and game (if provided) belong to the user */
    public function createForKid(User $user, Kid $kid): bool
    {
        return $kid->user_id === $user->id;
    }

    /** Check that the game belongs to the user */
    public function createForGame(User $user, Game $game): bool
    {
        return $game->user_id === $user->id;
    }
}

<?php

namespace App\Policies;

use App\Models\Kid;
use App\Models\User;

class KidPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Kid $kid): bool
    {
        return $kid->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Kid $kid): bool
    {
        return $kid->user_id === $user->id;
    }

    public function delete(User $user, Kid $kid): bool
    {
        return $kid->user_id === $user->id;
    }
}

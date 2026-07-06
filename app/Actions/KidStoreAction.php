<?php

namespace App\Actions;

use App\Http\Requests\KidStoreRequest;
use App\Models\Kid;
use App\Models\User;

class KidStoreAction
{
    public function execute(KidStoreRequest $request, User $user): Kid
    {
        return Kid::create([
            'name' => $request->input('name'),
            'user_id' => $user->id,
        ]);
    }
}

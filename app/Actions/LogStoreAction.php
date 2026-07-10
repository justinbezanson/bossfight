<?php

namespace App\Actions;

use App\Http\Requests\LogStoreRequest;
use App\Models\Log;
use App\Models\User;

class LogStoreAction
{
    public function execute(LogStoreRequest $request, User $user): Log
    {
        return Log::create([
            'log_date' => now(),
            'kid_id' => $request->input('kid_id'),
            'game_id' => $request->input('game_id'),
            'user_id' => $user->id,
            'message' => $request->input('message'),
        ]);
    }
}

<?php

namespace App\Actions;

use App\Http\Requests\KidUpdateRequest;
use App\Models\Kid;

class KidUpdateAction
{
    public function execute(KidUpdateRequest $request, Kid $kid): Kid
    {
        $kid->update([
            'name' => $request->input('name'),
        ]);

        return $kid;
    }
}

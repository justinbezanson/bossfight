<?php

namespace App\Actions;

use App\Models\Kid;

class KidDeleteAction
{
    public function execute(Kid $kid): void
    {
        $kid->delete();
    }
}

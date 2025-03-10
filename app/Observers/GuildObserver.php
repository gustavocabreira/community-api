<?php

namespace App\Observers;

use App\Models\Guild;

class GuildObserver
{
    public function creating(Guild $guild): void
    {
        if (auth()->user()) {
            $guild->user_id = auth()->user()->id;
        }
    }
}

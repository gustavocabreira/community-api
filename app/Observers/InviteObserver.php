<?php

namespace App\Observers;

use App\Models\Invite;
use Illuminate\Support\Str;

class InviteObserver
{
    public function creating(Invite $invite): void
    {
        if(!$invite->code) {
            $invite->code = Str::random(8);
        }
    }
}

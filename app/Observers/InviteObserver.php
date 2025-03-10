<?php

namespace App\Observers;

use App\Helpers\GenerateInviteCode;
use App\Models\Invite;

class InviteObserver
{
    public function creating(Invite $invite): void
    {
        if (! $invite->code) {
            $invite->code = GenerateInviteCode::execute();
        }
    }
}

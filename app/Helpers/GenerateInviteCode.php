<?php

namespace App\Helpers;

use App\Models\Invite;
use Illuminate\Support\Str;

class GenerateInviteCode
{
    public static function execute($code = null): string
    {
        $code ??= Str::random(8);

        while(Invite::query()->where('code', $code)->exists()) {
            $code = Str::random(8);
        }

        return $code;
    }
}

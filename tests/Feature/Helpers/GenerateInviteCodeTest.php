<?php

use App\Helpers\GenerateInviteCode;
use App\Models\Guild;
use App\Models\Invite;
use App\Models\User;

it('should be able to generate a random code', function () {
    $code = GenerateInviteCode::execute();

    expect($code)->toBeString()->and($code)->toHaveLength(8);
});

it('should generate a different code each time', function () {
    $code1 = GenerateInviteCode::execute();
    $code2 = GenerateInviteCode::execute();

    expect($code1)->not()->toBe($code2);
});

it('should generate a code that does not exist in the database', function () {
    $user = User::factory()->create();
    $guild = Guild::factory()->create(['user_id' => $user->id]);
    $invite = Invite::factory()->create(['guild_id' => $guild->id, 'code' => '12345678']);

    $code = GenerateInviteCode::execute($invite->code);

    expect(Invite::query()->where('code', $code)->exists())->toBeFalse();
});

<?php

use App\Models\Guild;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Response;

it('should be able to create an invite', function () {
    $model = new Invite;
    $user = User::factory()->create();
    $guild = Guild::factory()->create(['user_id' => $user->id]);

    $payload = [
        'code' => '12345678',
        'expires_at' => '2025-03-10 12:00:00',
    ];

    $response = $this->actingAs($user)->postJson(route('api.guilds.invites.store', $guild), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'id',
            'code',
            'expires_at',
            'guild_id',
        ]);

    expect($response->json('id'))->toBeInt()
        ->and($response->json('id'))->toBe(1)
        ->and($response->json('code'))->toBe($payload['code'])
        ->and($response->json('expires_at'))->toBe($payload['expires_at'])
        ->and($response->json('guild_id'))->toBe($guild->id);

    $this->assertDatabaseHas($model->getTable(), [
        'code' => $payload['code'],
        'expires_at' => $payload['expires_at'],
        'guild_id' => $guild->id,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

it('should be able to generate a random code if none is provided', function () {
    $model = new Invite;
    $user = User::factory()->create();
    $guild = Guild::factory()->create(['user_id' => $user->id]);

    $payload = [
        'expires_at' => '2025-03-10 12:00:00',
    ];

    $response = $this->actingAs($user)->postJson(route('api.guilds.invites.store', $guild), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'id',
            'code',
            'expires_at',
            'guild_id',
        ]);

    expect($response->json('id'))->toBeInt()
        ->and($response->json('id'))->toBe(1)
        ->and($response->json('code'))->toBeString()
        ->and($response->json('expires_at'))->toBe($payload['expires_at'])
        ->and($response->json('guild_id'))->toBe($guild->id);

    $this->assertDatabaseHas($model->getTable(), [
        'code' => $response->json('code'),
        'expires_at' => $payload['expires_at'],
        'guild_id' => $guild->id,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

it('should return the code has already been taken', function () {
    $model = new Invite;
    $user = User::factory()->create();
    $guild = Guild::factory()->create(['user_id' => $user->id]);
    $invite = Invite::factory()->create(['guild_id' => $guild->id, 'code' => '12345678']);

    $payload = [
        'code' => $invite->code,
        'expires_at' => '2025-03-10 12:00:00',
    ];

    $response = $this->actingAs($user)->postJson(route('api.guilds.invites.store', $guild), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['code']);

    expect($response->json('errors.code.0'))->toBe('The code has already been taken.');

    $this->assertDatabaseMissing($model->getTable(), [
        'code' => $payload['code'],
        'expires_at' => $payload['expires_at'],
        'guild_id' => $guild->id,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

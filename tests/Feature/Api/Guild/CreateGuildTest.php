<?php

use App\Models\Guild;
use App\Models\User;
use Illuminate\Http\Response;

it('should be able to create a guild', function () {
    $model = new Guild;
    $user = User::factory()->create();

    $payload = [
        'name' => 'Test Guild',
    ];

    $response = $this->actingAs($user)->postJson(route('api.guilds.store'), $payload);
    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'id',
            'name',
            'user_id',
        ]);

    expect($response->json('id'))->toBeInt()
        ->and($response->json('id'))->toBe(1)
        ->and($response->json('name'))->toBe($payload['name'])
        ->and($response->json('user_id'))->toBe($user->id);

    $this->assertDatabaseHas($model->getTable(), [
        'name' => $payload['name'],
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

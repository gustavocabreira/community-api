<?php

use App\Models\User;
use Illuminate\Http\Response;

it('should be able to get the current user', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
    ]);

    $payload = [
        'email' => $user->email,
        'password' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.login'), $payload);

    $token = $response->json('token');

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->getJson(route('api.user.current-user'), []);

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'id',
            'name',
            'email',
        ]);

    expect($response->json('id'))->toBeInt()
        ->and($response->json('id'))->toBe($user->id)
        ->and($response->json('name'))->toBe($user->name)
        ->and($response->json('email'))->toBe($user->email);
});

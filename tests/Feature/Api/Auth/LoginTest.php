<?php

use App\Models\User;
use Illuminate\Http\Response;

it('should be able to login using the email and password', function () {
    $model = new User;

    $existingUser = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
    ]);
    $payload = [
        'email' => $existingUser->email,
        'password' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.login'), $payload);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'token',
        ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

it('should return invalid credentials', function () {
    $model = new User;
    $payload = [
        'email' => 'test@example.com',
        'password' => 'invalid',
    ];

    $response = $this->postJson(route('api.auth.login'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJsonStructure([
            'message',
        ]);

    expect($response->json('message'))->toBe('Invalid credentials.');

    $this->assertDatabaseCount($model->getTable(), 0);
});

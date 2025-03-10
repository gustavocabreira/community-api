<?php

use App\Models\User;
use Illuminate\Http\Response;

it('should be able to register a new user and retrieve the token', function () {
    $model = new User;
    $payload = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure([
            'token',
        ]);

    expect($response->json('token'))->toBeString();

    $this->assertDatabaseHas($model->getTable(), [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

it('should return the email has already been taken', function () {
    $model = new User;

    $existingUser = User::factory()->create();
    $payload = [
        'name' => fake()->name(),
        'email' => $existingUser->email,
        'password' => 'P@ssw0rd',
        'password_confirmation' => 'P@ssw0rd',
    ];

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['email']);

    expect($response->json('errors.email.0'))->toBe('The email has already been taken.');

    $this->assertDatabaseMissing($model->getTable(), [
        'name' => $payload['name'],
        'email' => $payload['email'],
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
});

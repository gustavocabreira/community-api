<?php

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('a', 256)], ['name' => 'The name field must be at most 255 characters.'],
    ],
    'empty email' => [
        ['email' => ''], ['email' => 'The email field is required.'],
    ],
    'invalid email' => [
        ['email' => 'invalid'], ['email' => 'The email field is invalid.'],
    ],
    'email with more than 255 characters' => [
        ['email' => Str::repeat('a', 256).'@email.com'], ['email' => 'The email field must be at most 255 characters.'],
    ],
    'empty password' => [
        ['password' => ''], ['password' => 'The password field is required.'],
    ],
    'password with less than 8 characters' => [
        ['password' => Str::repeat('a', 7)], ['password' => 'The password field must be at least 8 characters.'],
    ],
    'password is weak' => [
        ['password' => 'password'], ['password' => [
            'The password field confirmation does not match.',
            'The password field must contain at least one uppercase and one lowercase letter.',
            'The password field must contain at least one symbol.',
            'The password field must contain at least one number.',
        ]],
    ],
]);

it('should return unprocessable entity if the payload is invalid', function (array $payload, array $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new User;

    $response = $this->postJson(route('api.auth.register'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');

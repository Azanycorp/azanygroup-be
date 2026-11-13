<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('user can log in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson(route('auth.login'), [
        'email' => 'jane@example.com',
        'password' => 'password123',
        'device_name' => 'iphone',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'is_email_verified',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'email' => 'jane@example.com',
                    'is_email_verified' => true,
                ],
            ],
        ]);

    expect($response->json('data.access_token'))->toBeString()->not->toBeEmpty();
});

test('user cannot log in with invalid password', function () {
    User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    $response = $this->postJson(route('auth.login'), [
        'email' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user cannot log in with non-existent email', function () {
    $response = $this->postJson(route('auth.login'), [
        'email' => 'missing@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login requires email and password', function () {
    $response = $this->postJson(route('auth.login'), []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('authenticated user can view their profile via me endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson(route('auth.me'));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                ],
            ],
        ]);
});

test('unauthenticated user cannot access me endpoint', function () {
    $response = $this->getJson(route('auth.me'));

    $response->assertUnauthorized();
});

test('authenticated user can log out and revoke their token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-device')->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('auth.logout'));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);

    expect($user->tokens()->count())->toBe(0);
});

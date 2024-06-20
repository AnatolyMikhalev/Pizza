<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function test_register_a_user()
    {
        $response = $this->json('POST','/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone_number' => '1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully registered']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function test_register_with_missing_fields_422_expected()
    {
        $response = $this->json('POST','/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function test_register_with_existing_email_422_expected()
    {
        $existingUser = User::factory()->create();

        $response = $this->json('POST','/api/auth/register', [
            'name' => 'John Doe',
            'email' => $existingUser->email,
            'password' => 'password123',
            'phone_number' => '1234567890',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function test_login_user_can_to_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->json('POST','/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    /** @test */
    public function test_login_with_invalid_credentials_401_expected()
    {
        $user = User::factory()->create();

        $response = $this->json('POST','/api/auth/login', [
            'email' => $user->email,
            'password' => ' ',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized']);
    }

    /** @test */
    public function test_user_can_get_authenticated_user()
    {
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST','/api/auth/user');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /** @test */
    public function test_user_without_token_401_expected()
    {
        $response = $this->json('POST','/api/auth/user');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function test_logout_a_user()
    {
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST','/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    /** @test */
    public function test_logout_without_token_401_expected()
    {
        $response = $this->json('POST','/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function test_refresh_a_token()
    {
        $user = User::factory()->create();

        $token = auth()->login($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->json('POST', '/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    /** @test */
    public function test_refresh_token_without_token_401_expected()
    {
        $response = $this->json('POST', '/api/auth/refresh');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
}

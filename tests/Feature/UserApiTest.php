<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_retrieve_their_profile_information()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/profile');

        $response->assertStatus(Response::HTTP_OK); // Assert a successful response
        $response->assertJsonFragment([ // Assert specific data is present
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Test creating a new user.
     *
     * @return void
     */
    public function test_can_create_a_user()
    {
        // 1. Arrange: Define the data for the new user
        $userData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 2. Act: Send an HTTP POST request to the endpoint
        $response = $this->postJson('/api/register', $userData);

        // 3. Assert: Check the response status and the database
        $response->assertStatus(Response::HTTP_CREATED); // 201 Created
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
        $response->assertJsonFragment([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
        ]);
    }

    /**
     * Test successful user login.
     *
     * @return void
     */
    public function test_user_can_login_with_valid_credentials()
    {
        // 1. Arrange: Create a test user in the database
        $password = 'password123';
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make($password),
        ]);

        // 2. Act: Make a POST request to the login endpoint
        $response = $this->postJson('/api/login', [
            'email' => 'testuser@example.com',
            'password' => $password,
        ]);

        // 3. Assert: Check the response and authentication status
        $response->assertStatus(Response::HTTP_OK) // Asserts a successful HTTP status
                 ->assertJsonStructure([
                     'message',
                     'status',
                     'data',
                 ]); // Asserts the response JSON structure

        // Assert that the user is authenticated in the application
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user login with invalid credentials.
     *
     * @return void
     */
    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Arrange: No user created, or use wrong credentials
        // In this case, we rely on the database being empty (via RefreshDatabase)

        // Act: Make a POST request to the login endpoint with invalid credentials
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert: Check for an error response and that the user is not authenticated
        $response->assertStatus(Response::HTTP_BAD_REQUEST) // Asserts an unauthorized status
                 ->assertJson([
                    'status' => 'fail',
                    'message' => 'Invalid credentials'
                 ]);

        $this->assertGuest(); // Asserts that no user is logged in
    }

    public function test_an_authenticated_user_can_log_out()
    {
        $user = User::factory()->create();

        // Use Sanctum::actingAs() to authenticate the user with the 'api' guard
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/logout'); // Assuming your logout route is /api/logout

        $response->assertStatus(Response::HTTP_OK);
    }
}

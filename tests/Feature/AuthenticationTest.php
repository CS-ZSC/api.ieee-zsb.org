<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Position;
use App\Models\Chapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run seeders needed for authentication
        $this->artisan('db:seed', ['--class' => 'PermissionSeeder']);
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'ChapterSeeder']);
        $this->artisan('db:seed', ['--class' => 'PositionSeeder']);
    }

    /**
     * Test dashboard login with token authentication
     */
    public function test_dashboard_login_returns_token()
    {
        // Create a test user
        $ieeeChapter = Chapter::where('short_name', 'IEEE')->first();

        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'groupable_type' => 'chapter',
            'groupable_id' => $ieeeChapter->id,
        ]);

        // Assign Dev position for admin access
        $devPosition = Position::where('name', 'Dev')->first();
        $user->positions()->attach($devPosition->id);
        $user->assignDefaultRole();

        // Test dashboard login
        $response = $this->postJson('/api/dashboard/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'positions',
                    'roles',
                ]
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test site login with token authentication
     */
    public function test_site_login_returns_token()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/site/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'data' => [
                    'id',
                    'name',
                    'email',
                ]
            ]);

        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * Test eventsgate registration creates verification record
     */
    public function test_eventsgate_registration_creates_verification()
    {
        // Test registration
        $response = $this->postJson('/api/eventsgate/register', [
            'name' => 'Visitor User',
            'email' => 'visitor@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '01234567890',
            'national_id' => '12345678901234',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registration data received. Please check your email for 6-digit verification code.',
                'email' => 'visitor@test.com',
                'verification_sent' => true,
            ]);

        // Verify verification record was created (user is created after verification)
        $verification = \App\Models\EmailVerification::where('email', 'visitor@test.com')->first();
        $this->assertNotNull($verification);
        $this->assertNotEmpty($verification->verification_code);
    }

    /**
     * Test eventsgate login with visitor user
     */
    public function test_eventsgate_visitor_login()
    {
        // Create a visitor user directly (simulating verified registration)
        $visitorPosition = Position::where('name', 'Visitor')->first();

        $user = User::create([
            'name' => 'Visitor User',
            'email' => 'visitor@test.com',
            'password' => bcrypt('password123'),
            'phone_number' => '01234567890',
            'national_id' => '12345678901234',
            'email_verified_at' => now(),
        ]);

        $user->positions()->attach($visitorPosition->id);
        $user->assignDefaultRole();

        // Test login
        $loginResponse = $this->postJson('/api/eventsgate/login', [
            'email' => 'visitor@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'positions',
                ]
            ]);

        $this->assertNotEmpty($loginResponse->json('token'));
    }

    /**
     * Test authenticated routes require valid token
     */
    public function test_authenticated_routes_require_token()
    {
        // Test dashboard route without token
        $response = $this->getJson('/api/dashboard/chapters');
        $response->assertStatus(401);

        // Test eventsgate route without token
        $response = $this->postJson('/api/eventsgate/events/1/register');
        $response->assertStatus(401);
    }

    /**
     * Test authenticated routes work with valid token
     */
    public function test_authenticated_routes_work_with_valid_token()
    {
        $ieeeChapter = Chapter::where('short_name', 'IEEE')->first();

        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'groupable_type' => 'chapter',
            'groupable_id' => $ieeeChapter->id,
        ]);

        $devPosition = Position::where('name', 'Dev')->first();
        $user->positions()->attach($devPosition->id);
        $user->assignDefaultRole();

        // Get token
        $loginResponse = $this->postJson('/api/dashboard/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        // Use token to access protected route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/dashboard/chapters');

        $response->assertStatus(200);
    }

    /**
     * Test route parameters work correctly
     */
    public function test_route_parameters_work_correctly()
    {
        $chapter = Chapter::first();

        // Test public route with parameter
        $response = $this->getJson("/api/site/chapters/{$chapter->id}");
        $response->assertStatus(200)
            ->assertJson([
                'id' => $chapter->id,
                'name' => $chapter->name,
            ]);
    }

    /**
     * Test logout invalidates token
     */
    public function test_logout_invalidates_token()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
        ]);

        // Login
        $loginResponse = $this->postJson('/api/site/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');

        // Logout
        $logoutResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/site/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully',
            ]);

        // Verify token is invalidated (tokens are deleted on logout)
        $this->assertEquals(0, $user->tokens()->count());
    }
}

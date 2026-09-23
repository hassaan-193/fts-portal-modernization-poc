<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;

/**
 * AuthenticationIntegrationTest
 *
 * Integration tests for the staff web login flow.
 * These tests exercise the full HTTP stack: route → middleware → controller → response.
 * The schema is rebuilt fresh for every test in the FeatureTestCase base class.
 *
 * What is tested:
 *  1. Public /login page is accessible without auth.
 *  2. Root / redirects unauthenticated users to /login.
 *  3. Valid credentials log in and redirect to dashboard.
 *  4. Wrong password returns a validation error and keeps user as guest.
 *  5. Empty email field fails validation before hitting the DB.
 *  6. Authenticated user can access the dashboard.
 *  7. POST /logout clears the session.
 */
class AuthenticationIntegrationTest extends FeatureTestCase
{
    // ------------------------------------------------------------------
    // 1. Public routes are accessible without authentication
    // ------------------------------------------------------------------

    /**
     * GET /login must return 200 for unauthenticated visitors.
     */
    public function testLoginPageIsPubliclyAccessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('email');
    }

    /**
     * GET / must redirect unauthenticated visitors to /login.
     */
    public function testRootRedirectsToLoginWhenUnauthenticated()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    // ------------------------------------------------------------------
    // 2. Successful login with valid credentials
    // ------------------------------------------------------------------

    /**
     * POST /login with correct credentials redirects to the dashboard.
     */
    public function testValidStaffLoginRedirectsToDashboard()
    {
        // ARRANGE: create a staff user in the throwaway test DB
        $user = User::create([
            'name'              => 'Test Staff',
            'email'             => 'staff@test.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // ACT: POST login form
        $response = $this->post('/login', [
            'email'    => 'staff@test.com',
            'password' => 'password123',
        ]);

        // ASSERT: redirected to home dashboard (not back to /login)
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    // ------------------------------------------------------------------
    // 3. Failed login with wrong password
    // ------------------------------------------------------------------

    /**
     * POST /login with wrong password stays on login with an error.
     */
    public function testInvalidPasswordShowsLoginError()
    {
        User::create([
            'name'              => 'Test Staff',
            'email'             => 'staff@test.com',
            'password'          => Hash::make('correct-password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email'    => 'staff@test.com',
            'password' => 'wrong-password',
        ]);

        // Must redirect back with validation error, not authenticate
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ------------------------------------------------------------------
    // 4. Validation: missing fields are rejected before DB is even hit
    // ------------------------------------------------------------------

    /**
     * POST /login with missing email triggers validation error.
     */
    public function testLoginRejectsEmptyEmail()
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ------------------------------------------------------------------
    // 5. Authenticated staff can access the dashboard
    // ------------------------------------------------------------------

    /**
     * Authenticated user can reach the home dashboard (GET /).
     */
    public function testAuthenticatedUserCanAccessDashboard()
    {
        $user = User::create([
            'name'     => 'Test Staff',
            'email'    => 'staff@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->get('/');

        // Must NOT redirect to /login
        $this->assertNotEquals('/login', $response->headers->get('Location'),
            'Authenticated user was incorrectly redirected to the login page.');
    }

    // ------------------------------------------------------------------
    // 6. Logout terminates the session
    // ------------------------------------------------------------------

    /**
     * POST /logout clears authentication and returns a redirect.
     * The app redirects to / after logout (which then redirects to /login
     * for unauthenticated users). We verify the critical fact: the user
     * is now a guest (session was cleared).
     */
    public function testLogoutClearsSession()
    {
        $user = User::create([
            'name'     => 'Test Staff',
            'email'    => 'staff@test.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)->post('/logout');

        // Critical: the session must have been cleared — the user is now a guest
        $this->assertGuest();
    }
}

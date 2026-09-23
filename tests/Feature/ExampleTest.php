<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUnauthenticatedUserIsRedirectedToLogin()
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function testLoginPageReturnsSuccessfulResponse()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}

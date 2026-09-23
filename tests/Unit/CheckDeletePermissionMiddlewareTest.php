<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\CheckDeletePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDeletePermissionMiddlewareTest extends TestCase
{
    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new CheckDeletePermission();
    }

    /**
     * Test non-DELETE requests (GET, POST, PUT) pass through without restriction.
     */
    public function testNonDeleteRequestsPassThrough()
    {
        $request = Request::create('/projects/1', 'GET');
        $passed = false;

        $response = $this->middleware->handle($request, function ($req) use (&$passed) {
            $passed = true;
            return response('OK', 200);
        });

        $this->assertTrue($passed);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test DELETE request without authenticated user is blocked and redirected back.
     */
    public function testDeleteWithoutAuthIsBlocked()
    {
        Auth::shouldReceive('user')->andReturn(null);

        $request = Request::create('/projects/1', 'DELETE');
        $passed = false;

        $response = $this->middleware->handle($request, function ($req) use (&$passed) {
            $passed = true;
            return response('OK', 200);
        });

        $this->assertFalse($passed);
        $this->assertTrue($response->isRedirection());
    }

    /**
     * Test DELETE request where user does NOT have 'deletes' permission is blocked.
     */
    public function testDeleteWithoutPermissionIsBlocked()
    {
        $mockUser = \Mockery::mock();
        $mockUser->shouldReceive('can')->with('deletes')->andReturn(false);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $request = Request::create('/projects/1', 'DELETE');
        $passed = false;

        $response = $this->middleware->handle($request, function ($req) use (&$passed) {
            $passed = true;
            return response('OK', 200);
        });

        $this->assertFalse($passed);
        $this->assertTrue($response->isRedirection());
    }

    /**
     * Test DELETE request where user HAS 'deletes' permission passes through.
     */
    public function testDeleteWithPermissionPassesThrough()
    {
        $mockUser = \Mockery::mock();
        $mockUser->shouldReceive('can')->with('deletes')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $request = Request::create('/projects/1', 'DELETE');
        $passed = false;

        $response = $this->middleware->handle($request, function ($req) use (&$passed) {
            $passed = true;
            return response('OK', 200);
        });

        $this->assertTrue($passed);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

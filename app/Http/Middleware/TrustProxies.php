<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * This site is served through Cloudflare, so every request reaches the
     * origin from a Cloudflare edge IP. With this left null, Laravel ignored
     * X-Forwarded-Proto and treated HTTPS traffic as plain HTTP — which made it
     * redirect to "correct" the scheme, and a redirect downgrades POST to GET.
     * The visible symptom was every POST under /api/ returning
     * "The GET method is not supported for this route. Supported methods: POST."
     *
     * '*' is appropriate here because the origin is only reachable through
     * Cloudflare. If the origin IP is ever exposed directly, replace this with
     * Cloudflare's published IP ranges: https://www.cloudflare.com/ips/
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}

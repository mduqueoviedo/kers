<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoApiKey
{
    /**
     * Require the configured demo API key as a Bearer token.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config()->string('kers.demo_api_key');

        abort_if($expectedKey === '', Response::HTTP_NOT_FOUND);

        $providedKey = $request->bearerToken();

        abort_unless(
            is_string($providedKey) && hash_equals($expectedKey, $providedKey),
            Response::HTTP_UNAUTHORIZED,
            'Invalid demo API key.',
        );

        return $next($request);
    }
}

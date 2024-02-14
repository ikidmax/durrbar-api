<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : url(env('FRONTEND_URL') . '/auth/login');
    }

    public function handle($request, Closure $next, ...$guards)
    {
        if ($jwt = $request->cookie('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $jwt);
        }

        $this->authenticate($request, $guards);

        return $next($request);
    }
}

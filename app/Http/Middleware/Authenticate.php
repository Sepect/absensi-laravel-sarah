<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * The path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API requests, return null to let handle() handle JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Redirect to admin login for all web requests
        return route('admin.login');
    }
}
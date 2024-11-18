<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DriverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('driver')->check() && auth('driver')->user()) {
            if (auth('driver')->user()->status == "approved") {
                if (auth('driver')->user()->role == 'driver' || auth('driver')->user()->role == 'both') {
                    return $next($request);
                } else {
                    return jsonResponse(false, 'Your driver profile is not exists.', 400);
                }
            } else {
                return jsonResponse(false, 'Your account is not approved by the admin yet. You will receive notification once your account is approved.', 400);
            }
        } else {
            return jsonResponse(false, 'Unauthorized!', 401);
        }
    }
}

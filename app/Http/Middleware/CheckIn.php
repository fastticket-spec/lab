<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class CheckIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->userRole() !== 'Checkin Users') {
            return response()->json(['message' => 'Only Checkin users can access this route'], 401);
        }

        return $next($request);
    }
}

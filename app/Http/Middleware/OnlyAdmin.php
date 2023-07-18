<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = auth()->user()->account;

        if (!$account) return redirect('/');

        $role = optional($account->role)->role;

        if ($account->role_id && $role !== 'Admin Users') return redirect('/');

        return $next($request);
    }
}

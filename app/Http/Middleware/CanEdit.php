<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanEdit
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = auth()->user()->account;

        if (!$account) return redirect('/');

        $role = optional($account->role)->role;

        if ($account->role_id && ($role !== 'Admin Users' && $role !== 'Editors')) return redirect('/');

        return $next($request);
    }
}

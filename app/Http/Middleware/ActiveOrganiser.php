<?php

namespace App\Http\Middleware;

use App\Helpers\Flash;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveOrganiser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) return redirect('login');

        if (!auth()->user()->account->active_organiser) {
            Flash::notify('Please choose an organiser to continue!', 'danger');
            return redirect('/organisers');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // BIARKAN LOGIN PAGE TETAP BISA DIAKSES
        if ($request->is('login')) {
            return $next($request);
        }

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return redirect($user->getDashboardUrl());
        }
        return $next($request);
    }
}

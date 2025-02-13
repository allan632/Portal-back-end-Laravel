<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckProfile
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();
        $profile = $user->profiles->map(function ($profile) {
            return $profile->name;
        });
        if (!$user || !$profile === $role ) {
            return response()->json(['message' => $profile,$role], 403);
        }


        return $next($request);
    }
}

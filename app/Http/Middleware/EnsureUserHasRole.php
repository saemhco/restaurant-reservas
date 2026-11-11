<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /** @param  Closure(Request): Response  $next */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole($roles)) {
            return ApiResponse::error('Forbidden. Insufficient role.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

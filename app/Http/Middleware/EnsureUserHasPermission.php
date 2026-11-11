<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /** @param  Closure(Request): Response  $next */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasPermission($permission)) {
            return ApiResponse::error('Forbidden. Missing permission.', Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

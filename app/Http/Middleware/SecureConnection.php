<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        return $next($request);
        if ($request->isSecure() || $request->getHost() === 'localhost' || str_ends_with($request->getHost(), '.test')) {
        }

        return response()->json([ 'error' => 'Request must be sent over HTTPS', ], 400);


    }
}

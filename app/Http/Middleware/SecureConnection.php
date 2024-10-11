<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function Laravel\Prompts\error;

class SecureConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->isSecure() || $request->getHost() === 'localhost' || str_ends_with($request->getHost(), '.test')) {
        }

        return response()->json([ 'error' => 'Request must be sent over HTTPS ' . $request->getHost(), ], 400);
        return $next($request);


    }
}

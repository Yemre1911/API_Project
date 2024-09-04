<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class TrustHosts
{
    /**
     * Determine if the given request has a trusted host.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function handle(Request $request, \Closure $next)
    {
        return $next($request);
    }
}

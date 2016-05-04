<?php

namespace App\Http\Middleware;

use Closure;

class RecordAuthcate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //Data sends from adruino has been encrypted(AES or Sha1), which should be verified here
        return $next($request);
    }
}

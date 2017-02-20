<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('home');
<<<<<<< HEAD
=======

>>>>>>> ba045595f44a630f23913d926284dcd1f49686e3
    }
}

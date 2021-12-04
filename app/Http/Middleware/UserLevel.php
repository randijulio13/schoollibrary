<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guest())
            return redirect('/');

        if (auth()->user()->level != 'admin')
            return redirect('home');

        return $next($request);
    }
}

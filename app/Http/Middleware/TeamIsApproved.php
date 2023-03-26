<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!$request->user()?->currentTeam?->approved_at) {
          return Redirect('/dashboard');
        } else {
          return $next($request);
        }
    }
}

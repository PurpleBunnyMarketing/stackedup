<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserPaidOrNot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if ($user->type == 'company') {
            // flash('Purchase Subscription First then you can access other resources')->error();
            return redirect(route('social-media-list'));
        }
        return $next($request);
    }
}

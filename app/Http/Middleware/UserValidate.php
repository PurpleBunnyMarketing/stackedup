<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserValidate
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
		if (Auth::user()->is_active != 'y') {

			flash('Your account has been deactivated by admin!')->warning()->important();
			Auth::logout();
			return redirect(route('login'));
		}
		return $next($request);
	}
}

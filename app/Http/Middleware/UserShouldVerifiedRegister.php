<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserShouldVerifiedRegister
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
       $response = $next($request);
        if (Auth::check() && !Auth::user()->status) {

        Auth::logout();

        Session::flash("flash_notification", [
        "level" => "warning",
       "message" => "Registrasi berhasil.<br>Tunggu konfirmasi admin untuk login."
        ]);

        return redirect('/login');
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class WeChatAuthenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $openid = Session::get('openid','');
        if ($this->auth->guest() || empty($openid)) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                //dd( Auth::user());
                return redirect()->guest('/msc/wechat/user/user-login');
            }
        }
        return $next($request);
    }
}

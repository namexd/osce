<?php

namespace Modules\Osce\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Entities\SysUserRole;

class BillboardLoginMiddleware
{
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //如果没有登录就回到前页
        try {
            if (!$user = $this->auth->user()) {
                throw new \Exception('当前操作者没有登陆');
            }

            /*
             * 获取权限
             */
            $userRoles = SysUserRole::where('user_id', '=', $user->id)->orderBy('role_id')->get();

            if ($userRoles->isEmpty()) {
                throw new \Exception('非法用户，请按照要求注册');
            }

            /**
             * 获取sp老师和监考老师还有超管的权限
             */
            $invigilatorRoleId = config('osce.invigilatorRoleId');
            $spRoleId = config('osce.spRoleId');
            foreach ($userRoles as $userRole) {
                if ($userRole->role_id == $invigilatorRoleId || $userRole->role_id == $spRoleId) {
                    return $next($request);
                }
            }
            throw new \Exception('当前登陆者不是监考老师或sp老师');
        } catch (\Exception $ex) {
            return redirect()->route('osce.billboard.login.getIndex')->withErrors($ex->getMessage());
        }
    }
}

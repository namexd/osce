<?php
/**
 * Created by PhpStorm.
 * User: wangjiang
 * Date: 2016/3/24 0024
 * Time: 14:53
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
//use Illuminate\Support\Facades\Auth;
use Auth;
use Modules\Osce\Repositories\QuestionBankRepositories;

class TeacherRedirectIfAuthenticated
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
        if (!Auth::check()) {
            return $next($request);
        }

        $questionBankRepositories = new QuestionBankRepositories();
        $roleType = $questionBankRepositories->getExamLoginUserRoleType();

        if ($roleType == 1) {
            return redirect()->route('osce.admin.ApiController.LoginAuthWait');
        }

        if ($roleType == 2) {
            return redirect()->route('osce.admin.ApiController.getStudentExamIndex');
        }


        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Managers\UserManager;
use Auth;
use JsonResult;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */


    protected $_userManager;

    public function __construct(UserManager $userManager)
    {
        $this->_userManager = $userManager;
    }

    /**
     * @param $request
     * @param Closure $next
     * @param $requiredRoles - строка, содержащия псевдонимы ролей через "|" i.e.(admin,student)
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function handle($request, Closure $next,$requiredRoles)
    {
        try {

            $userRole = $this->_userManager->getCurrentUserInfo()->getRole();
            $access = false;

            $requiredRoles = explode('|',$requiredRoles);

            foreach ($requiredRoles as $requiredRole) {
                if ($userRole === $requiredRole) {
                    $access = true;
                }
            }
            if($access) {
                return $next($request);
            }
            else{
                return new Response(view('errors.503'));
            }

        } catch (\Exception $exception) {

            return new Response(view('errors.503'));
        }


    }


}

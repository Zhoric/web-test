<?php

namespace App\Http\Middleware;

use Closure;
use Managers\UserManager;
use Auth;
use UserRole;
use Helpers\RoleHelper;
use Exception;

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
    public function handle($request, Closure $next, $requiredRoles)
    {
        try {
            $userRole = $this->_userManager->getCurrentUserInfo()->getRole();
            $isAccess = $this->isAccessableRoute($requiredRoles, $userRole);

            if ($isAccess) {
                return $next($request);
            } else {

                return redirect(RoleHelper::getDefaultRoleRoute($userRole));
            }

        } catch (Exception $exception) {
            return redirect(RoleHelper::getDefaultRoleRoute(UserRole::Guest));
        }
    }


    private function isAccessableRoute($requiredRoles, $userRole)
    {

        $access = false;

        $requiredRoles = explode('|', $requiredRoles);

        foreach ($requiredRoles as $requiredRole) {
            if ($userRole === $requiredRole) {
                $access = true;
            }
        }
        return $access;
    }


}

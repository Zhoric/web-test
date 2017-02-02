<?php

namespace App\Http\Controllers;

use Helpers\RoleHelper;
use Managers\UserManager;
use Exception;
use UserRole;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $_userManager;

    public function __construct(UserManager $userManager)
    {
        $this->middleware('auth');
        $this->_userManager = $userManager;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userRole = $this->_userManager->getCurrentUserInfo()->getRole();
            return redirect(RoleHelper::getDefaultRoleRoute($userRole));
        }
        catch(Exception $e){
            return redirect(RoleHelper::getDefaultRoleRoute(UserRole::Guest));
        }
    }
}

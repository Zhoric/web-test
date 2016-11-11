<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Managers\UserManager;
use Mockery\CountValidator\Exception;


class UserController extends Controller
{

    protected $userManager;
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getRoleByUser(){

        $user = Auth::user();
        if(isset($user)) {
            try {
                $role = $this->userManager->getUserRole($user->getId());
                if ($role == null){
                    throw new Exception('Невозможно определить роль пользователя!');
                }
                return json_encode(['result' => $role->getSlug(), 'success' => true]);
            }
            catch (Exception $e)
            {
                return json_encode(['result' => $e->getMessage(), 'success' => false]);
            }
        }
        else {
            return json_encode(['result' => 'Пользователь не авторизован!', 'success' => false]);
        }
    }

}
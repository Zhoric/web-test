<?php

namespace Helpers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Repositories\UnitOfWork;
use UserRole;

class AuthHelper
{
    public static function tryGetCurrentUserRole(UnitOfWork $unitOfWork){
        $currentUser = Auth::user();
        if (!isset($currentUser)){
            throw new Exception("Действие доступно только для авторизованных пользователей.");
        }
        $role = $unitOfWork->userRoles()->getRoleByUser($currentUser->getId());
        if (isset($role)) {
            return $role->getSlug();
        } else throw new Exception('Невозможно получить роль текущего пользователя.');
    }

    public static function isCurrentUserInRole(UnitOfWork $unitOfWork, $role){
        return self::tryGetCurrentUserRole($unitOfWork) === $role;
    }
}
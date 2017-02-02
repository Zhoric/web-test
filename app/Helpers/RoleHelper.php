<?php


namespace Helpers;
use UserRole;

class RoleHelper
{

    public static function getDefaultRoleRoute($userRole)
    {
        switch ($userRole) {
            case UserRole::Student : {
                return '/home';
            }

            case UserRole::Admin : {
                return '/admin';
            }

            case UserRole::Lecturer : {
                return '/disciplines';
            }

            case UserRole::Guest :
            default : {
                return '/login';
            }

        }
    }
}
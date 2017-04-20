<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use Illuminate\Http\Request;
use Managers\UserManager;


class UserController extends Controller
{

    protected $userManager;
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getRoleByUser(){
        try{
            $user = Auth::user();
            if(isset($user)) {
                try {
                    $role = $this->userManager->getUserRole($user->getId());
                    if ($role == null){
                        throw new Exception('Невозможно определить роль пользователя!');
                    }
                    return $this->successJSONResponse($role->getSlug());
                } catch (Exception $exception)
                {
                    $this->faultJSONResponse($exception->getMessage());
                }
            }
            else {
                throw new Exception('Пользователь не авторизован!');
            }
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getCurrentUserInfo(){
        try{
            $currentUserInfo = $this->userManager->getCurrentUserInfo();
            return $this->successJSONResponse($currentUserInfo);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function activate($userId){
        try{
            $this->userManager->activate($userId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getStudentInfo($id){
        try{
            $studentInfo = $this->userManager->getStudentInfo($id);
            return $this->successJSONResponse($studentInfo);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function setUserPassword(Request $request){
        try{
            $userId = $request->json('userId');
            $password = $request->json('password');

            $user = $this->userManager->getUser($userId);
            $user->setPassword(bcrypt($password));
            $this->userManager->update($user);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function setCurrentUserPassword(Request $request){
        try{
            $oldPassword = $request->json('oldPass');
            $newPassword = $request->json('newPass');
            $this->userManager->setPassword($oldPassword, $newPassword);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteUser($id){
        try{
            $this->userManager->deleteUser($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getByNameAndGroupPaginated(Request $request){
        try{
            $pageNum =  $request->query('page');
            $pageSize = $request->query('pageSize');
            $name = $request->query('name');
            $groupId = $request->query('groupId');
            $isActive = $request->query("isActive");

            $paginationResult = $this->userManager
                ->getByNameAndGroupPaginated($pageSize, $pageNum, $name, $groupId, $isActive);

            return $this->successJSONResponse($paginationResult);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


}
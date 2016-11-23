<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use Managers\OrgStructureManager;
use Profile;

class OrgStructureController extends Controller
{
    private $_orgStructureManager;

    public function __construct(OrgStructureManager $orgStructureManager)
    {
        $this->_orgStructureManager = $orgStructureManager;
    }

    public function getAllInstitutes()
    {
        try{
            $institutes = $this->_orgStructureManager->getInstitutes();
            return $this->successJSONResponse($institutes);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllProfiles(){
        try{
            $profiles = $this->_orgStructureManager->getProfiles();
            return $this->successJSONResponse($profiles);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getInstituteProfiles($id)
    {
        try{
            $profiles = $this->_orgStructureManager->getInstituteProfiles($id);
            return $this->successJSONResponse($profiles);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getProfileGroups($id){
        try{
            $profileGroups = $this->_orgStructureManager->getProfileGroups($id);
            return $this->successJSONResponse($profileGroups);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getProfilePlans($id){
        try{
            $profilePlans = $this->_orgStructureManager->getProfilePlans($id);
            return $this->successJSONResponse($profilePlans);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getProfileDisciplines($id){
        try{
            $disciplines = $this->_orgStructureManager->getProfileDisciplines($id);
            return $this->successJSONResponse($disciplines);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteProfile($id){
        try{
            $this->_orgStructureManager->deleteProfile($id);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    /*
     * Пример валидного JSON:
     * method: POST
     * url: http://www.web-test.ru/api/org/profile/create
     * body: { "profile": {"name":"test","semesters":5,"fullname":"fullnameTEST"} }
     */
    public function createProfile(Request $request){
        try{
            $profileData = $request->json('profile');
            $profile = new Profile();
            $profile->fillFromJson($profileData);
            $this->_orgStructureManager->addProfile($profile);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function updateProfile(Request $request){
        try{
            $profileData = $request->json('profile');
            $profile = new Profile();
            $profile->fillFromJson($profileData);
            $this->_orgStructureManager->updateProfile($profile);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

}

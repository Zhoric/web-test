<?php

namespace Managers;

use Illuminate\Support\Facades\Auth;
use Institute;
use Profile;
use Repositories\UnitOfWork;
use Helpers\AuthHelper;
use UserRole;
/*
 * Огранизационная структура ВУЗа (институты, профили)
 */
class OrgStructureManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getInstitutes(){
        return $this->_unitOfWork->institutes()->all();
    }

    public function getProfiles(){
        return $this->_unitOfWork->profiles()->all();
    }

    public function getInstituteProfiles($instituteId){
        return $this->_unitOfWork->profiles()->getByInstitute($instituteId);
    }

    public function getProfileGroups($profileId){
        return $this->_unitOfWork->groups()->getGroupsByProfile($profileId);
    }

    public function getProfilePlans($profileId){
        return $this->_unitOfWork->studyPlans()
            ->where('Studyplan.profile = '.$profileId);
    }

    public function getProfileDisciplines($profileId){

        $lecturerId = null;
        $isLecturer = AuthHelper::isCurrentUserInRole($this->_unitOfWork, UserRole::Lecturer);

        if ($isLecturer) {
            $lecturerId = Auth::user()->getId();
        }

        return $this->_unitOfWork
            ->disciplines()
            ->getByProfile($profileId, $lecturerId);
    }

    public function addInstitute(Institute $institute){

    }

    public function updateInstitute(Profile $profile){

    }

    public function addProfile(Profile $profile){
        $this->_unitOfWork->profiles()->create($profile);
        $this->_unitOfWork->commit();
    }

    public function updateProfile(Profile $profile){
        $this->_unitOfWork->profiles()->update($profile);
        $this->_unitOfWork->commit();
    }

    public function deleteProfile($profileId){
        $profile = $this->_unitOfWork->profiles()->find($profileId);
        if ($profile != null){
            $this->_unitOfWork->profiles()->delete($profile);
            $this->_unitOfWork->commit();
        }
    }
}
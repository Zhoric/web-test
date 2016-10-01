<?php

namespace Managers;

use App\models\Group;
use App\models\Profile;
use Helpers\GroupHelper;
use Repositories\GroupRepository;
use Repositories\ProfileRepository;
use Repositories\UserRepository;

class ProfileManager
{
    private $_profileRepo;

    public function __construct(ProfileRepository $profileRepo)
    {
        $this->_profileRepo = $profileRepo;
    }

    public function getAllProfiles()
    {
        return $this->_profileRepo->all();
    }

    public function addProfile($profileName, $semestersCount)
    {
        $profile = new Profile();
        $profile->name = $profileName;
        $profile->semesters = $semestersCount;

        $this->_profileRepo->create($profile);
    }

    public function deleteProfile($id)
    {
        $profile = $this->_profileRepo->find($id);
        if ($profile != null){
            $this->_profileRepo->delete($profile);
        }
    }

    public function getProfile($id)
    {
        return $this->_profileRepo->findWith($id, 'groups');
    }

    public function updateProfile($id, $name, $semesters)
    {
        $profile = new Profile();
        $profile->id = $id;
        $profile->name = $name;
        $profile->semesters = $semesters;

        $this->_profileRepo->update($profile);
    }
}
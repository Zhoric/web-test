<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Managers\ProfileManager;

class ProfileController extends Controller
{
    private $_profileManager;

    public function __construct(ProfileManager $profileManager)
    {
        $this->_profileManager = $profileManager;
    }

    public function index()
    {
        return json_encode($this->_profileManager->getAllProfiles());
    }

    public function store(Request $request)
    {
        $profile = $request->json('profile');
        $this->_profileManager->addProfile($profile['name'], $profile['semesters']);
    }


    public function show($id)
    {
        return json_encode($this->_profileManager->getProfile($id));
    }

    public function update(Request $request, $id)
    {
        $profile = $request->json('profile');
        $this->_profileManager->updateProfile($id, $profile['name'], $profile['semesters']);
    }

    public function destroy($id)
    {
        $this->_profileManager->deleteProfile($id);
    }
}

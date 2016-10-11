<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Managers\OrgStructureManager;

class OrgStructureController extends Controller
{
    private $_orgStructureManager;

    public function __construct(OrgStructureManager $orgStructureManager)
    {
        $this->_orgStructureManager = $orgStructureManager;
    }

    public function index()
    {
        return json_encode($this->_orgStructureManager->getInstitutes());
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function getInstituteProfiles($id)
    {
        return json_encode($this->_orgStructureManager->getInstituteProfiles($id));
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Managers\GroupManager;

class GroupController extends Controller
{
    private $_groupManager;

    public function __construct(GroupManager $groupManager)
    {
        $this->_groupManager = $groupManager;
    }

    public function index()
    {
        return json_encode($this->_groupManager->getAll());
    }

    public function store(Request $request)
    {

    }

    public function show($id)
    {

    }

    public function getProfileGroups($id)
    {
        return json_encode($this->_groupManager->getProfileGroups($id));
    }

    public function update(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }
}

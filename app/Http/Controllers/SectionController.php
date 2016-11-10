<?php

namespace App\Http\Controllers;

use Managers\SectionManager;
use Illuminate\Http\Request;
use Section;

class SectionController extends Controller
{
    private $_sectionManager;

    public function __construct(SectionManager $sectionManager)
    {
        $this->_sectionManager = $sectionManager;
    }

    public function getAllSectionsByTheme($themeId){
         return json_encode($this->_sectionManager->getSectionsByTheme($themeId));
    }

    public function getSection($id){
        return json_encode($this->_sectionManager->getSection($id));
    }

    public function create(Request $request){
        $sectionData = $request->json('section');
        $themeId = $request->json('themeId');

        $section = new Section();
        $section->fillFromJson($sectionData);
        $this->_sectionManager->addSection($section, $themeId);
    }

    public function update(Request $request){
        $sectionData = $request->json('section');
        $themeId = $request->json('themeId');

        $section = new Section();
        $section->fillFromJson($sectionData);
        $this->_sectionManager->updateSection($section, $themeId);
    }

    public function delete($sectionId){
        $this->_sectionManager->deleteSection($sectionId);
    }



}
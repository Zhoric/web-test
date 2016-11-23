<?php

namespace App\Http\Controllers;

use Exception;
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
        try{
            $sections = $this->_sectionManager->getSectionsByTheme($themeId);
            return $this->successJSONResponse($sections);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllSectionsByDiscipline($disciplineId){
        try{
            $sections = $this->_sectionManager->getSectionsByDiscipline($disciplineId);
            return $this->successJSONResponse($sections);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getSection($id){
        try{
            $section = $this->_sectionManager->getSection($id);
            return $this->successJSONResponse($section);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $sectionData = $request->json('section');
            $themeId = $request->json('themeId');
            $disciplineId = $request->json('disciplineId');

            $section = new Section();
            $section->fillFromJson($sectionData);
            $this->_sectionManager->addSection($section, $themeId, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $sectionData = $request->json('section');
            $themeId = $request->json('themeId');
            $disciplineId = $request->json('disciplineId');

            $section = new Section();
            $section->fillFromJson($sectionData);
            $this->_sectionManager->updateSection($section, $themeId, $disciplineId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($sectionId){
        try{
            $this->_sectionManager->deleteSection($sectionId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }



}
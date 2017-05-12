<?php

namespace App\Http\Controllers;

use Exception;
use Managers\MediableManager;
use Illuminate\Http\Request;
use Mediable;

class MediableController extends Controller
{
    private $_mediableManager;

    public function __construct(MediableManager $mediableManager)
    {
        $this->_mediableManager = $mediableManager;
    }

    public function getAllByMedia($mediaId){
        try{
            $mediables = $this->_mediableManager->getByMedia($mediaId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllByTheme($themeId){
        try{
            $mediables = $this->_mediableManager->getByTheme($themeId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllByThemeAndMedia(Request $request){
        try{
            $themeId = $request->query('theme');
            $mediaId = $request->query('media');
            $mediables = $this->_mediableManager->getByThemeAndMedia($themeId, $mediaId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllByDiscipline($disciplineId){
        try{
            $mediables = $this->_mediableManager->getByDiscipline($disciplineId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllByDisciplineAndMedia(Request $request){
        try{
            $disciplineId = $request->query('discipline');
            $mediaId = $request->query('media');
            $mediables = $this->_mediableManager->getByDisciplineAndMedia($disciplineId, $mediaId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getMediable($id){
        try{
            $mediable = $this->_mediableManager->getMediable($id);
            return $this->successJSONResponse($mediable);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $mediableData = $request->json('mediable');
            $mediaId = $request->json('mediaId');
            $themeId = $request->json('themeId');
            $discipline = $request->json('discipline');

            $mediable = new Mediable();
            $mediable->fillFromJson($mediableData);
            $this->_mediableManager->addMediable($mediable, $mediaId, $themeId, $discipline);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $mediableData = $request->json('mediable');
            $mediaId = $request->json('mediaId');
            $themeId = $request->json('themeId');
            $discipline = $request->json('discipline');

            $mediable = new Mediable();
            $mediable->fillFromJson($mediableData);
            $this->_mediableManager->updateMediable($mediable, $mediaId, $themeId, $discipline);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($mediableId){
        try{
            $this->_mediableManager->deleteMediable($mediableId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }



}
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

    public function getAllMediablesByMedia($mediaId){
        try{
            $mediables = $this->_mediableManager->getMediablesByMedia($mediaId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllMediablesByTheme($themeId){
        try{
            $mediables = $this->_mediableManager->getMediablesByTheme($themeId);
            return $this->successJSONResponse($mediables);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getAllMediablesByDiscipline($disciplineId){
        try{
            $mediables = $this->_mediableManager->getMediablesByDiscipline($disciplineId);
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
            $disciplineId = $request->json('disciplineId');

            $mediable = new Mediable();
            $mediable->fillFromJson($mediableData);
            $this->_mediableManager->addMediable($mediable, $mediaId, $themeId, $disciplineId);
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
            $disciplineId = $request->json('disciplineId');

            $mediable = new Mediable();
            $mediable->fillFromJson($mediableData);
            $this->_mediableManager->updateMediable($mediable, $mediaId, $themeId, $disciplineId);
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
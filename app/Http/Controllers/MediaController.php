<?php

namespace App\Http\Controllers;

use Exception;
use Managers\MediaManager;
use Illuminate\Http\Request;
use Media;

class MediaController extends Controller
{
    private $_mediaManager;

    public function __construct(MediaManager $mediaManager)
    {
        $this->_mediaManager = $mediaManager;
    }

    public function getMedia($id){
        try{
            $media = $this->_mediaManager->getMedia($id);
            return $this->successJSONResponse($media);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function getMediaByHash($hash){
        try{
            $media = $this->_mediaManager->getMediaByHash($hash);
            return $this->successJSONResponse($media);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function create(Request $request){
        try{
            $mediaData = $request->json('media');
            $media = new Media();
            $media->fillFromJson($mediaData);
            $this->_mediaManager->addMedia($media);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function update(Request $request){
        try{
            $mediaData = $request->json('media');
            $media = new Media();
            $media->fillFromJson($mediaData);
            $this->_mediaManager->updateMedia($media);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function delete($mediaId){
        try{
            $this->_mediaManager->deleteMedia($mediaId);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

    public function deleteFile(Request $request){
        try{
            $path = $request->json('path');
            $this->_mediaManager->deleteFile($path);
            return $this->successJSONResponse();
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }


}
<?php

namespace Managers;

use Repositories\UnitOfWork;
use Media;
use DocxReader;

class MediaManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getMedia($id){
        return $this->_unitOfWork->medias()->find($id);
    }

    public function getMediaByHash($hash){
        return $this->_unitOfWork->medias()->findByHash($hash);
    }

    public function addMedia(Media $media){
        $this->_unitOfWork->medias()->create($media);
        $this->_unitOfWork->commit();
    }

    public function updateMedia(Media $media){
        $this->_unitOfWork->medias()->update($media);
        $this->_unitOfWork->commit();
    }

    public function deleteMedia($mediaId){
        $media = $this->_unitOfWork->medias()->find($mediaId);
        if ($media != null){
            $this->_unitOfWork->medias()->delete($media);
            $this->_unitOfWork->commit();
        }
    }

    public function deleteFile($path){
        if (file_exists($path))
            unlink(public_path($path));
    }

    public function addDocx(Media $media){
        $doc = new DocxReader();
        $doc->setFile($media->getPath());
        if(!$doc->get_errors()) {
            $html = $doc->toHtml();
            $media->setContent($html);
            $this->_unitOfWork->medias()->create($media);
            $this->_unitOfWork->commit();
        }
    }

}
<?php

namespace Managers;

use League\Flysystem\Exception;
use Repositories\UnitOfWork;
use Media;
use DocxReader;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

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
        $oldMedia = $this->_unitOfWork->medias()->find($media->getId());
        if ($media->getType() == 'text' && file_exists('upload/.wordImage/' . $oldMedia->getHash())){
            if ($media->getName() != $oldMedia->getName()){
                $doc = new DocxReader();
                $media->setContent($doc->changeImagesPath($media->getContent(), 'upload/.wordImage/' . $media->getHash()));
                rename('upload/' . $oldMedia->getName(), 'upload/' . $media->getName());
                rename('upload/.wordImage/' . $oldMedia->getHash(), 'upload/.wordImage/' . $media->getHash());
            }
        }
        $this->_unitOfWork->medias()->update($media);
        $this->_unitOfWork->commit();
    }

    public function deleteMedia($mediaId){
        $media = $this->_unitOfWork->medias()->find($mediaId);
        if ($media != null){
            if ($media->getType() == 'text' && file_exists('upload/.wordImage/' . $media->getHash())){
                $dir = 'upload/.wordImage/' . $media->getHash();
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach($files as $file) {
                    if ($file->isDir()){
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dir);
            }
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
        $path = 'upload/.wordImage/' . $media->getHash();
        $oldmask = umask(0);
        mkdir($path, 0777);
        umask($oldmask);

        $doc->loadImages($media->getPath(), $path);
        $html = $doc->toHtml();
        $media->setContent($html);
        $this->_unitOfWork->medias()->create($media);
        $this->_unitOfWork->commit();
    }

}
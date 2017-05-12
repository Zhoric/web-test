<?php

namespace Managers;

use Repositories\UnitOfWork;
use Mediable;

class MediableManager
{
    private $_unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->_unitOfWork = $unitOfWork;
    }

    public function getByTheme($themeId){
        return $this->_unitOfWork->mediables()->getByTheme($themeId);
    }

    public function getByThemeAndMedia($themeId, $mediaId){
        return $this->_unitOfWork->mediables()->getByThemeAndMedia($themeId, $mediaId);
    }

    public function getByDiscipline($disciplineId){
        return $this->_unitOfWork->mediables()->getByDiscipline($disciplineId);
    }

    public function getByDisciplineAndMedia($disciplineId, $mediaId){
        return $this->_unitOfWork->mediables()->getByDisciplineAndMedia($disciplineId, $mediaId);
    }

    public function getByMedia($mediaId){
        return $this->_unitOfWork->mediables()->getByMedia($mediaId);
    }

    public function getMediable($id){
        return $this->_unitOfWork->mediables()->find($id);
    }

    public function addMediable(Mediable $mediable, $mediaId, $themeId, $disciplineId){
        $media = $this->_unitOfWork->medias()->find($mediaId);
        $mediable->setMedia($media);

        if ($disciplineId != null) {
            $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
            $mediable->setDiscipline($discipline);
        }
        if ($themeId != null) {
            $theme = $this->_unitOfWork->themes()->find($themeId);
            $mediable->setTheme($theme);
        }

        $this->_unitOfWork->mediables()->create($mediable);
        $this->_unitOfWork->commit();
    }

    public function updateMediable(Mediable $mediable, $mediaId, $themeId, $disciplineId){
        $media = $this->_unitOfWork->medias()->find($mediaId);
        $mediable->setMedia($media);

        if ($disciplineId != null) {
            $discipline = $this->_unitOfWork->disciplines()->find($disciplineId);
            $mediable->setDiscipline($discipline);
        }
        if ($themeId != null) {
            $theme = $this->_unitOfWork->themes()->find($themeId);
            $mediable->setTheme($theme);
        }

        $this->_unitOfWork->mediables()->update($mediable);
        $this->_unitOfWork->commit();
    }

    public function deleteMediable($mediableId){
        $mediable = $this->_unitOfWork->mediables()->find($mediableId);
        if ($mediable != null){
            $this->_unitOfWork->mediables()->delete($mediable);
            $this->_unitOfWork->commit();
        }
    }


}
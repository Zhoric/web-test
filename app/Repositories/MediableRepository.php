<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Mediable;

class MediableRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Mediable::class);
    }

    public function getByTheme($themeId){
        return $this->repo->findBy(['theme' => $themeId]);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId, 'theme' => null]);
    }

    public function getByMedia($mediaId){
        return $this->repo->findBy(['media' => $mediaId]);
    }


}
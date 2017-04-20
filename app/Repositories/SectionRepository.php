<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Section;

class SectionRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Section::class);
    }

    public function getByTheme($themeId){
        return $this->repo->findBy(['theme' => $themeId]);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId, 'theme' => null]);
    }
}
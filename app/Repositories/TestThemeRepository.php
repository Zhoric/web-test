<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use TestTheme;
class TestThemeRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestTheme::class);
    }

    public function getByTest($testId){
        return $this->repo->findBy(['test' => $testId]);
    }
}
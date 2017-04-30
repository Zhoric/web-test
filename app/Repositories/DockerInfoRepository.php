<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use DockerInfo;

class DockerInfoRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, DockerInfo::class);
    }

    public function findByLang($lang){

        $query = $this->repo->createQueryBuilder($this->model)->where("DockerInfo.lang = $lang");

        return $query->getQuery()->execute();
    }
}
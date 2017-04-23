<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Media;

class MediaRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Media::class);
    }

    public function findByHash($hash){
        return $this->repo->findBy(['hash' => $hash]);
    }
}
<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Profile;

class ProfileRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Profile::class);
    }

    public function getByInstitute($instituteId){
        return $this->repo->findBy(['institute' => $instituteId]);
    }
}
<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Role;

class RoleRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Role::class);
    }

    /**
     * Получение роли по псевдониму.
     * @param $slug
     * @return null|object
     */
    public function getBySlug($slug){
        return $this->repo->findOneBy(['slug' => $slug]);
    }

}
<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use RoleUser;

class RoleUserRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, RoleUser::class);
    }
}
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

    /**
     * @param $id
     * @return \Role
     */
    public function getRoleByUser($id){
        $userRole = $this->repo->findOneBy(['user' => $id]);

        if(isset($userRole)){
            return $userRole->getRole();
        }
        else return null;
    }
}
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
            $roleId = $userRole->getRole()->getId();

            $roleQb = $this->em->getRepository(\Role::class)->createQueryBuilder('r');
            $role =  $roleQb->where('r.id ='.$roleId)->getQuery()->execute();
            return (isset($role) && !empty($role)) ? $role[0] : null;
        }
        else return null;
    }
}
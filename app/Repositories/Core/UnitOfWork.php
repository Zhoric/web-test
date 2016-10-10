<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 10.10.16
 * Time: 22:36
 */

namespace Repositories;


use Doctrine\ORM\EntityManager;


class UnitOfWork
{
    private $_em;

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    private $_usersRepo;

    public function getUsersRepo(){
        if ($this->_usersRepo == null){
            $this->_usersRepo = new UserRepository($this->_em);
        }
        return $this->_usersRepo;
    }

    public function commit(){
        $this->_em->flush();
    }
}
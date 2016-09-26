<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IUserRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'User');
    }
}
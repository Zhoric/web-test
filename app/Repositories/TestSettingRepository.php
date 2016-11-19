<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use PaginationResult;
use Repositories\Interfaces\IUserRepository;
use Doctrine\ORM\Query\Expr\Join;
use RoleUser;
use TestSetting;
use User;
use UserRole;

class TestSettingRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, TestSetting::class);
    }

    public function getByKey($key){
        return $this->repo->findOneBy(['key' => $key]);
    }
}
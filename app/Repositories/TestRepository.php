<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;

class TestRepository extends BaseRepository implements ITestRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Test');
    }
}
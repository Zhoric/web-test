<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;

class TestResultRepository extends BaseRepository implements ITestResultRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'TestResult');
    }
}
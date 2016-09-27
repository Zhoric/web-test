<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IDisciplineRepository;
use Repositories\Interfaces\IGroupRepository;

class DisciplineRepository extends BaseRepository implements IDisciplineRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Discipline');
    }
}
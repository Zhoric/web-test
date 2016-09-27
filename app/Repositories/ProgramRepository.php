<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;

class ProgramRepository extends BaseRepository implements IProgramRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Program');
    }
}
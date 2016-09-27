<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;

class GroupRepository extends BaseRepository implements IGroupRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Group');
    }
}
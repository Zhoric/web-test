<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use MarkType;
use TestMarkType;
use Theme;

class MarkTypeRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, MarkType::class);
    }
}
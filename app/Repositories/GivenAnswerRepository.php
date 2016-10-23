<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use GivenAnswer;

class GivenAnswerRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, GivenAnswer::class);
    }


}
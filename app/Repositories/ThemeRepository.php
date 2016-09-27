<?php

namespace Repositories;

use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IGroupRepository;

class ThemeRepository extends BaseRepository implements IThemeRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Theme');
    }
}
<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\DB;
use Test;
use TestTheme;

class TestRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Test::class);
    }

    public function getByDiscipline($disciplineId){
        return $this->repo->findBy(['discipline' => $disciplineId]);
    }

    function setTestThemes($testId, array $themeIds){
        $qb = $this->em->getRepository(TestTheme::class)->createQueryBuilder('tt');
        $deleteQuery = $qb->delete()
            ->where('tt.test = :test')
            ->setParameter('test', $testId)
            ->getQuery();

        $deleteQuery->execute();

        foreach ($themeIds as $themeId){
            DB::table('test_theme')
                ->insert(  array(
                    'test_id' => $testId,
                    'theme_id' => $themeId
                ));
        }
    }
}
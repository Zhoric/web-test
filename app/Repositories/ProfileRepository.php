<?php

namespace Repositories;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Illuminate\Support\Facades\DB;
use Profile;
use ProfileDiscipline;

class ProfileRepository extends BaseRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, Profile::class);
    }

    public function getByInstitute($instituteId){
        return $this->repo->findBy(['institute' => $instituteId]);
    }

    public function getByDisciplineProfilesIds($disciplineId){
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('ProfileDiscipline', 'dp');
        $rsm->addFieldResult('dp','profile_id','profile_id');

        $sql = " select profile_id
            from profile_discipline as dp
            where dp.discipline_id = ".intval($disciplineId);


        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
<?php

namespace Repositories;

use App\models\Discipline;
use Illuminate\Support\Facades\DB;
use ProAI\Datamapper\EntityManager;
use Repositories\Interfaces\IDisciplineRepository;
use Repositories\Interfaces\IGroupRepository;

class DisciplineRepository extends BaseRepository implements IDisciplineRepository
{
    public function __construct(EntityManager $em)
    {
        parent::__construct($em, 'Discipline');
    }

    function updateLecturerDisciplines($lecturerId, $disciplinesIds)
    {
        DB::table('discipline_lecturer')
            ->where('user_id', $lecturerId)
            ->delete();

        foreach ($disciplinesIds as $disciplineId){
            DB::table('discipline_lecturer')
                ->insert(['user_id' => $lecturerId,
                'discipline_id' => $disciplineId]);
        }
    }

    function updateDisciplineProfiles($disciplineId, $profilesIds)
    {
        DB::table('discipline_profile')
            ->where('discipline_id', $disciplineId)
            ->delete();

        foreach ($profilesIds as $profileId){
            DB::table('discipline_profile')
                ->insert(['profile_id' => $profileId,
                    'discipline_id' => $disciplineId]);
        }
    }

}
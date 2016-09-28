<?php

namespace Repositories\Interfaces;

interface IDisciplineRepository extends IRepository
{
    function updateLecturerDisciplines($lecturerId, $disciplineIds);

    function updateDisciplineProfiles($disciplineId, $profilesIds);
}
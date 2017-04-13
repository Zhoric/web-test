<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 04.11.16
 * Time: 1:43
 */

namespace Managers;

use DateTime;
use Exception;
use Repositories\UnitOfWork;
use TestEngine\GlobalTestSettings;

class ScheduleManager
{
    private $_unitOfWork;

    private $_settingsManager;

    private $semestersCount = 2;

    public function __construct(UnitOfWork $unitOfWork, SettingsManager $settingsManager)
    {
        $this->_unitOfWork = $unitOfWork;
        $this->_settingsManager = $settingsManager;
    }

    /**
     * Определение номера текущего семестра для группы.
     * @param $groupId
     * @return mixed
     */
    public function getCurrentSemesterForGroup($groupId){
        $group = $this->_unitOfWork->groups()->find($groupId);
        $groupStudyYear = $group->getCourse();
        $now = new DateTime();

        // +1, т.к. нумерация месяцев в этом формате даты идёт с нуля.
        $currentMonthNumber = date("n", $now->getTimestamp()) + 1;

        $firstSemesterStartMonth = $this->_settingsManager->get(GlobalTestSettings::firstSemesterMonthKey);
        $secondSemesterStartMonth = $this->_settingsManager->get(GlobalTestSettings::secondSemesterMonthKey);

        $currentYearSemester = ($currentMonthNumber >= $secondSemesterStartMonth
            && $currentMonthNumber < $firstSemesterStartMonth) ? 2 : 1;

        return ($groupStudyYear - 1) * $this->semestersCount + $currentYearSemester;
    }

    public function getCurrentSemesterForUser($userId){
        $userGroup = $this->_unitOfWork->studentGroups()->getUserGroup($userId);
        if ($userGroup == null || $userGroup->getGroup() == null){
            throw new Exception("Не найдена группа текущего пользователя!");
        }
        $groupId = $userGroup->getGroup()->getId();

        return $this->getCurrentSemesterForGroup($groupId);
    }
}
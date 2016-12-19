<?php

namespace TestEngine;

use Helpers\NameHelper;
use Illuminate\Redis\Database;
use Managers\TestManager;
use Managers\UserManager;
use TestSessionStatus;

class TestSessionTracker
{
    /**
     * Префикс ключа для отслеживания сессии тестирования.
     */
    const sessionTrackingPrefix = "TS_";

    /**
     * @var Database
     */
    protected $redisClient;

    /**
     * @var TestSessionFactory
     */
    protected $testSessionFactory;

    /**
     * @var TestManager
     */
    protected $testManager;

    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(
        Database $redisClient,
        TestSessionFactory $sessionFactory,
        TestManager $testManager,
        UserManager $userManager)
    {
        $this->redisClient = $redisClient;
        $this->testSessionFactory = $sessionFactory;
        $this->testManager = $testManager;
        $this->userManager = $userManager;
    }

    /**
     * Добавление записи о сессии тестирования для возможности отслеживания
     * процесса прохождения теста в режиме реального времени.
     * @param $sessionId - Идентификатор сессии, используется для формирования ключа.
     */
    public function trackSession($sessionId){
        $this->redisClient->set(self::sessionTrackingPrefix.$sessionId, TestSessionStatus::InProgress);
        $this->redisClient->expireat($sessionId, strtotime(GlobalTestSettings::testSessionCacheExpiration));
    }

    /**
     * Финализация сессии тестирования по окончанию теста.
     * @param $sessionId - Идентификатор сессии, используется для формирования ключа.
     */
    public function finalizeSession($sessionId){
        $this->redisClient->set(self::sessionTrackingPrefix.$sessionId, TestSessionStatus::Done);
        $this->redisClient->expireat($sessionId, strtotime(GlobalTestSettings::testSessionCacheExpiration));
    }

    /**
     * Получение данных о текущих сессиях тестирования.
     * @param null $requestedState - Требуемое состояние сессий (в процессе/окончена)
     * @return array
     */
    public function getCurrentSessions($requestedState = null){
        $currentSessionsInfo = [];
        $sessionIds = $this->getCurrentSessionsIds($requestedState);

        foreach ($sessionIds as $sessionId){
            $session = $this->testSessionFactory->getBySessionId($sessionId);
            $studentInfo = $this->userManager->getStudentInfo($session->getUserId());
            $test = $this->testManager->getById($session->getTestId());
            $studentFullName = NameHelper::concatFullName($studentInfo->getFirstName(),
                $studentInfo->getMiddleName(), $studentInfo->getLastName());
            $disciplineName = $test->getDiscipline()->getName();

            $testProcessInfo = new TestProcessInfo();
            $testProcessInfo->setAllQuestions($session->getAllQuestionsIds());
            $testProcessInfo->setAnsweredQuestions($session->getAnsweredQuestionsIds());
            $testProcessInfo->setGroupName($studentInfo->getGroup()->getName());
            $testProcessInfo->setStudentName($studentFullName);
            $testProcessInfo->setTestName($test->getSubject());
            $testProcessInfo->setDisciplineName($disciplineName);

            array_push($currentSessionsInfo, $testProcessInfo);
        }

        return $currentSessionsInfo;
    }

    private function getCurrentSessionsIds($requestedState = null){
        $sessionKeys = $this->redisClient->keys(self::sessionTrackingPrefix.'*');
        $sessionIds = [];

        foreach ($sessionKeys as $sessionKey){
            $sessionId = $this->extractSessionId($sessionKey);
            if (isset($requestedState)){
                $sessionState = $this->redisClient->get($sessionKey);

                if ($sessionState == $requestedState){
                    array_push($sessionIds, $sessionId);
                }
            } else {
                array_push($sessionIds, $sessionId);
            }
        }

        return $sessionIds;
    }

    /**
     * Извлечение идентификатора сессии из ключа кэша.
     * @param $redisKey
     * @return string
     */
    private function extractSessionId($redisKey){
        return substr($redisKey, strpos($redisKey, self::sessionTrackingPrefix) - strlen(self::sessionTrackingPrefix));
    }
}
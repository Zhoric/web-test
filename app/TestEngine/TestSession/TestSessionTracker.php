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
        $this->redisClient->set($this->generateSessionTrackingKey($sessionId), TestSessionStatus::InProgress);
        $this->redisClient->expireat($sessionId, strtotime(GlobalTestSettings::testSessionTrackingCacheExpiration));
    }

    /**
     * Финализация сессии тестирования по окончанию теста.
     * @param $sessionId - Идентификатор сессии, используется для формирования ключа.
     */
    public function finalizeSession($sessionId){
        $this->redisClient->set($this->generateSessionTrackingKey($sessionId), TestSessionStatus::Done);
        $this->redisClient->expireat($sessionId, strtotime(GlobalTestSettings::testSessionTrackingCacheExpiration));
    }

    /**
     * Получение данных о текущих сессиях тестирования.
     * @param $testId - Идентификатор интересующего теста.
     * @param $groupId - Идентфикатор интересующей группы.
     * @param null $requestedState - Требуемое состояние сессий (в процессе/окончена)
     * @return array
     */
    public function getCurrentSessions($testId, $groupId, $requestedState = null){
        $currentSessionsInfo = [];
        $sessionIds = $this->getCurrentSessionsIds($testId, $requestedState);

        foreach ($sessionIds as $sessionId){
            $session = $this->testSessionFactory->getBySessionId($sessionId);
            $studentInfo = $this->userManager->getStudentInfo($session->getUserId());
            $studentGroupId = $studentInfo->getGroup()->getId();

            // Игнорируем студентов, не относящихся к указанной группе.
            if ($studentGroupId != $groupId){
                continue;
            }

            $test = $this->testManager->getById($session->getTestId());
            $studentFullName = NameHelper::concatFullName($studentInfo->getFirstName(),
                $studentInfo->getMiddleName(), $studentInfo->getLastName());

            $testProcessInfo = new TestProcessInfo();
            $testProcessInfo->setAllQuestions($session->getAllQuestionsIds());
            $testProcessInfo->setAnsweredQuestions($session->getAnsweredQuestionsIds());
            $testProcessInfo->setStudentName($studentFullName);
            $testProcessInfo->setTestName($test->getSubject());
            $testProcessInfo->setMark(TestResultCalculator::calculateIntermediateResult($session->getTestResultId()));

            array_push($currentSessionsInfo, $testProcessInfo);
        }

        return $currentSessionsInfo;
    }

    /**
     * Получение списка идентификаторов текущих/недавно завершенных сессий тестирования.
     * Паттерн для поиска интересующих идентификаторов в хранилище Redis Cache составляется следующим образом:
     * {ПРЕФИКС}{ИДЕНТИФИКАТОР_ТЕСТА}-*
     * Так как идентификатор сессии тестирования составляется из идентфикатора теста и  идентификатора тестируемого,
     * такой паттерн позволяет выбрать из хранилища Redis Cache только сессии, относящиеся к интересующему нас тесту.
     * Например, в Redis могут храниться следующие сессии тестирования:
     * TS_5_33 - 5 тест, 33 пользователь.
     * TS_3_26 - 3 тест, 26 пользователь и т.д.
     * TS_3_33
     * TS_1_53
     * При помощи паттерна 'TS_3_*' мы сможем получить все сессии тестирования, относящиеся к 3 тесту. А именно:
     * TS_3_26
     * TS_3_33
     * @param $testId - Идентификатор теста.
     * @param null $requestedState
     * @return array
     */
    private function getCurrentSessionsIds($testId, $requestedState = null){
        $sessionKeys = $this->redisClient->keys(self::sessionTrackingPrefix.$testId.'-*');
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
        return substr($redisKey, strpos($redisKey, self::sessionTrackingPrefix) + strlen(self::sessionTrackingPrefix));
    }

    /**
     * Генерация ключа для хранения в Redis Cache информации о текущей сессии тестировании.
     * @param $sessionId
     * @return string
     */
    private function generateSessionTrackingKey($sessionId){
        return self::sessionTrackingPrefix.$sessionId;
    }
}
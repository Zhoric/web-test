<?php

namespace App\Http\Controllers;

use Exception;
use TestEngine\TestSessionTracker;

class TestTrackingController extends Controller
{
    /**
     * @var TestSessionTracker
     */
    private $_testSessionTracker;

    public function __construct(TestSessionTracker $testSessionTracker)
    {
        $this->_testSessionTracker = $testSessionTracker;
    }

    public function showSessions(){
        try{
            $sessions = $this->_testSessionTracker->getCurrentSessions();
            return $this->successJSONResponse($sessions);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
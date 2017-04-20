<?php

namespace App\Http\Controllers;

use Exception;
use TestEngine\TestSessionTracker;
use Illuminate\Http\Request;

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

    public function showSessions(Request $request){
        try{
            $testId = $request->query('testId');
            $state = $request->query('state');
            $groupId = $request->query('groupId');

            $sessions = $this->_testSessionTracker->getCurrentSessions($testId, $groupId, $state);

            return $this->successJSONResponse($sessions);
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use JsonResult;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function successJSONResponse($data = null, $message = null){
        return json_encode(new JsonResult(true, $data, $message));
    }

    protected function faultJSONResponse($message = null){
        return json_encode(new JsonResult(false, null, $message));
    }
}

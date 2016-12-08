<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Попытка получения данных о текущем пользователе.
     * В случае неудачи будет выброшено соответствующее исключение.
     * @return string
     */
    protected function tryGetCurrentUser(){
        $currentUser = Auth::user();
        if (isset($currentUser)){
            return $currentUser;
        } else {
            throw new Exception('Для данного действия необходима авторизация!');
        }
    }

    protected function tryGetCurrentUserId(){
        return $this->tryGetCurrentUser()->getId();
    }
}

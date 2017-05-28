<?php

namespace App\Http\Middleware;

use Closure;
use ConnectionConfigSettings;

class CheckIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        if($this->checkIfIpInWhiteList($request)){
            return $next($request);
        }
        throw new \Exception("IP ADDRESS NOT IN THE WHITELIST");
    }

    private function checkIfIpInWhiteList($request){

        foreach(ConnectionConfigSettings::$WHITE_LIST as $ip){
            if($request->ip() == $ip){
                return true;
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Managers\AuthManager;
use Exception;
use UserRole;
use Auth;
use Managers\UserManager;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    // protected $redirectTo = '/auth';
    protected $_authManager;
    protected $_userManager;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager, UserManager $userManager)
    {
        $this->_authManager = $authManager;
        $this->_userManager = $userManager;
    }

    public function login(Request $request)
    {

        try {
            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {

                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }
            $credentials = ['email' => $request->json('email'), 'password' => $request->json('password')];

            if(Auth::check()){
                throw new Exception('Вы уже вошли под другим пользователем!');
            }

            if (!$this->_authManager->checkIfEmailExists($request->json('email'))) {
                throw new Exception('Такого пользователя не существует!');
            }
            if (!$this->_authManager->checkIfUserActive($request->json('email'))) {
                throw new Exception('Ваш аккаунт ещё не подтвержден администратором!');
            }
            if ($this->guard()->attempt($credentials, $request->has('remember'))) {
                try {
                    $userRole = $this->_userManager->getCurrentUserInfo()->getRole();
                    return $this->successJSONResponse($userRole, 'Успешная попытка логина!');
                } catch (Exception $exception) {
                    return $this->faultJSONResponse($exception->getMessage());
                }
            }
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            throw new Exception('Неудачная попытка логина!');

        } catch (Exception $exception) {
            return $this->faultJSONResponse($exception->getMessage());
        }


    }



}

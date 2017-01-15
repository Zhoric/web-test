<?php

namespace App\Http\Controllers\Auth;

use Managers\AuthManager;
use Managers\GroupManager;
use Exception;
use User;
use UserRole;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
   // protected $redirectTo = '/home';
    protected $authManager;
    protected $groupManager;


    /**
     * Create a new controller instance.
     *
     * @param AuthManager $authManager
     * @param GroupManager $groupManager
     */
    public function __construct(AuthManager $authManager, GroupManager $groupManager){

        $this->authManager = $authManager;
        $this->groupManager = $groupManager;
        $this->middleware('guest');
    }


    /**
     * Регистрация нового пользователя.
     * @param Request $request
     * @return null|object
     */
    protected function create(Request $request)
    {

        try{
            $userData = $request->json('user');
            $groupId = $request->json('groupId');
            $role = $request->json('role');

            // По умолчанию регистрируем пользователя с ролью студента.
            if (!isset($role)){
                $role = UserRole::Student;
            }

            $user = new User();
            $user->fillFromJson($userData);

            $createdUser = $this->authManager->createNewUser($user, $role, false);

            if(isset($createdUser)) {
                $this->groupManager->setStudentGroup($groupId, $createdUser->getId());
            }
            else {
                throw new Exception('Ошибка при создании пользователя.');
            }

            return $createdUser;
        } catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }

    }

    public function checkIfEmailExists(Request $request){

        try {
            $email = $request->json('email');
            $exists = $this->authManager->checkIfEmailExists($email);
            return $this->successJSONResponse($exists);
        }
        catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }

    }

    public function register(Request $request){
        try {
            $user = $this->create($request);
            if(empty($user)){
                throw new Exception('Ошибка при регистрации');
            }
            event(new Registered($user));
            //TODO:: после регистрации не залогинивать юзера. Это заявка на регистрацию
            $this->guard()->login($user);


            $message = 'Ваша заявка на регистрацию принята! Ждите';
            return $this->successJSONResponse($message);
        }
        catch (Exception $exception){
            return $this->faultJSONResponse($exception->getMessage());
        }
    }

}

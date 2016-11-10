<?php

namespace App\Http\Controllers\Auth;

use Managers\AuthManager;
use Repositories\UnitOfWork;
use User;
use Repositories\UserRepository;
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
    protected $redirectTo = '/';
    protected $authManager;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthManager $authManager){

        $this->authManager = $authManager;
        $this->middleware('guest');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request)
    {
        $credentials = $request->json('user');
        $created_user = $this->authManager->createNewUser($credentials);
        return $created_user;
    }

    public function register(Request $request){
        $user = $this->create($request);
        if(empty($user)){
            return json_encode(['message' => 'Ошибка при регистрации!']);
        }
        event(new Registered($user));
        //TODO:: после регистрации не залогинивать юзера. Это заявка на регистрацию
        $this->guard()->login($user);

        return json_encode(['message' => 'Ваша заявка на регистрацию принята! Ждите']);
    }

}

<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Repositories\UserRepository;
use Validator;
use App\Http\Controllers\Controller;
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

    protected $user_repo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $user_repo)
    {

        $this->user_repo = $user_repo;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {


       return Validator::make($data, [
            'login' => 'required|max:255',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {


        $user = new User();
        $user->login = $data['login'];
        $user->password = bcrypt($data['password']);
        $user->fullName = 'TEST';
        $user->groupId = 1;
        $user->admissionYear = 2013;
        $user->role = 1;

         $this->user_repo->create($user);
        return $this->user_repo->findByLogin($user->login);

       /* return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);*/
    }
}

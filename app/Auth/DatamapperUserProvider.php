<?php namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\UserProvider;
use Repositories\UserRepository;


class DatamapperUserProvider implements UserProvider {

    protected $user_repo;

    public function __construct(UserRepository $user_repo)
    {

       $this->user_repo = $user_repo;
    }

    /**
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {


        return $this->user_repo->find($identifier);
        // Get and return a user by their unique identifier
    }

    /**
     * @param  mixed   $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        return $this->user_repo->findByRememberToken($identifier,$token);

        // Get and return a user by their unique identifier and "remember me" token
    }

    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        $user->setRememberToken($token);
        // Save the given "remember me" token for the given user
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {

        return $this->user_repo->findByCredentials($credentials);

        // Get and return a user by looking up the given credentials
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {


        $plain = $credentials['password'];
        $crypted = bcrypt($plain);

        dd($user->getAuthPassword());
        return $crypted ===  $user->getAuthPassword();
        // Check that given credentials belong to the given user
    }


}
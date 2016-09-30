<?php namespace App\Providers;

use App\models\User;
use App\Auth\DatamapperUserProvider;
use Illuminate\Support\ServiceProvider;
use ProAI\Datamapper\EntityManager;
use Repositories\UserRepository;

class DatamapperAuthProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->provider('datamapper',function()
        {
            $em = new EntityManager();
            return new DatamapperUserProvider(new UserRepository($em));
        });

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
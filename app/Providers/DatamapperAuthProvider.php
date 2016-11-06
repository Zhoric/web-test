<?php namespace App\Providers;

use App\Entities\User;
use App\Auth\DatamapperUserProvider;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\ServiceProvider;
use Repositories\UserRepository;

class DatamapperAuthProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    private $_em;

    public function boot(EntityManager $em)
    {
        $this->app['auth']->provider('datamapper',function()
        {
            return new DatamapperUserProvider(new UserRepository(app(EntityManager::class)));
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
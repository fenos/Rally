<?php namespace Fenos\Rally;

use Fenos\Rally\Models\Follower;
use Illuminate\Support\ServiceProvider;

class RallyServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('fenos/rally');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->rally();
        $this->repositories();
    }

    public function rally()
    {
        $this->app['rally'] = $this->app->share(function($app){

           return new Rally(

               $app->make('rally.repository'),
               $app['config']
           );
        });
    }

    public function repositories()
    {
        $this->app->bind('rally.repository', function($app){

            if ($this->app['config']->get('rally::polymorphic') !== false)
            {
                $bindClass = "\Fenos\Rally\Repositories\RallyPolymorphicRepository";
            }
            else
            {
                $bindClass = "\Fenos\Rally\Repositories\RallyRepository";
            }

           return new $bindClass(
              new Follower(),
               $app['db']
           );
        });

        $this->app->bind('Fenos\Rally\Repositories\RallyRepositoryInterface','rally.repository');

//        $this->app->bind('rally.polymorphic.repository', function($app){
//            return new Repositories\RallyPolymorphicRepository(
//                new Follower(),
//                $app['db']
//            );
//        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}

<?php

namespace Dugajean\Repositories\Providers;

use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Dugajean\Repositories\Console\Commands\MakeCriteriaCommand;
use Dugajean\Repositories\Console\Commands\MakeRepositoryCommand;
use Dugajean\Repositories\Console\Commands\Creators\CriteriaCreator;
use Dugajean\Repositories\Console\Commands\Creators\RepositoryCreator;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../../config/repositories.php';

        $this->publishes(
            [$configPath => config_path('repositories.php')],
            'repositories'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->registerMakeRepositoryCommand();
        $this->registerMakeCriteriaCommand();
        $this->commands(['command.repository.make', 'command.criteria.make']);
        $configPath = __DIR__ . '/../../config/repositories.php';

        $this->mergeConfigFrom(
            $configPath,
            'repositories'
        );
    }

    /**
     * Register the bindings.
     */
    protected function registerBindings()
    {
        $this->app->instance('FileSystem', new Filesystem());

        $this->app->bind('Composer', function ($app) {
            return new Composer($app['FileSystem']);
        });

        $this->app->singleton('RepositoryCreator', function ($app) {
            return new RepositoryCreator($app['FileSystem']);
        });

        $this->app->singleton('CriteriaCreator', function ($app) {
            return new CriteriaCreator($app['FileSystem']);
        });
    }

    /**
     * Register the make:repository command.
     */
    protected function registerMakeRepositoryCommand()
    {
        $this->app->singleton('command.repository.make', function ($app) {
            return new MakeRepositoryCommand($app['RepositoryCreator']);
        });
    }

    /**
     * Register the make:criteria command.
     */
    protected function registerMakeCriteriaCommand()
    {
        // Make criteria command.
        $this->app->singleton('command.criteria.make', function ($app) {
            return new MakeCriteriaCommand($app['CriteriaCreator']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.repository.make',
            'command.criteria.make',
        ];
    }
}

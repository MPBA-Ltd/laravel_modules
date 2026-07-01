<?php

namespace mpba\Modules;

use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\Exceptions\InvalidActivatorClass;
use mpba\Modules\Support\Stub;

class LaravelModulesServiceProvider extends ModulesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot(): void
    {
        $this->registerNamespaces();
        $this->registerModules();

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'modules-control');

        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'modules-migrations');

        $this->publishes([
            __DIR__ . '/satabase/Seeders' => database_path('seeders/vendor/modules'),
        ], 'modules-seeders');

        if ($this->app['config']->get('modules.admin.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();

        $this->app->singleton(\mpba\Modules\Support\DatabaseModuleRegistry::class);
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        $path = $this->app['config']->get('modules.stubs.path') ?? __DIR__.'/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->booted(function ($app) {
            /** @var RepositoryInterface $moduleRepository */
            $moduleRepository = $app[RepositoryInterface::class];
            if ($moduleRepository->config('stubs.enabled') === true) {
                Stub::setBasePath($moduleRepository->config('stubs.path'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('modules.activator');
            $class = $app['config']->get('modules.activators.'.$activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(RepositoryInterface::class, 'modules');
    }
}

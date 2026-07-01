<?php

namespace mpba\Modules;

use Illuminate\Support\ServiceProvider;
use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\Providers\BootstrapServiceProvider;
use mpba\Modules\Providers\ConsoleServiceProvider;
use mpba\Modules\Providers\ContractsServiceProvider;

abstract class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot() {}

    /**
     * Register all modules.
     */
    public function register() {}

    /**
     * Register all modules.
     */
    protected function registerModules(): void
    {
        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces(): void
    {
        $configPath = __DIR__.'/../config/config.php';

        $this->mergeConfigFrom($configPath, 'modules');
        $this->publishes([
            $configPath => config_path('modules.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    abstract protected function registerServices();

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [RepositoryInterface::class, 'modules'];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ContractsServiceProvider::class);
    }
}

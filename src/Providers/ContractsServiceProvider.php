<?php

namespace mpba\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}

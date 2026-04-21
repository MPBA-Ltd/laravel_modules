<?php

namespace mpba\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use mpba\Modules\Commands\CommandMakeCommand;
use mpba\Modules\Commands\ComponentMakeCommand;
use mpba\Modules\Commands\ControllerMakeCommand;
use mpba\Modules\Commands\DisableCommand;
use mpba\Modules\Commands\DumpCommand;
use mpba\Modules\Commands\EnableCommand;
use mpba\Modules\Commands\EventMakeCommand;
use mpba\Modules\Commands\FactoryMakeCommand;
use mpba\Modules\Commands\HelperMakeCommand;
use mpba\Modules\Commands\InstallCommand;
use mpba\Modules\Commands\JobMakeCommand;
use mpba\Modules\Commands\ListCommand;
use mpba\Modules\Commands\ListenerMakeCommand;
use mpba\Modules\Commands\MailMakeCommand;
use mpba\Modules\Commands\MiddlewareMakeCommand;
use mpba\Modules\Commands\MigrateCommand;
use mpba\Modules\Commands\MigrateRefreshCommand;
use mpba\Modules\Commands\MigrateResetCommand;
use mpba\Modules\Commands\MigrateRollbackCommand;
use mpba\Modules\Commands\MigrateStatusCommand;
use mpba\Modules\Commands\MigrationMakeCommand;
use mpba\Modules\Commands\ModelMakeCommand;
use mpba\Modules\Commands\ModuleDeleteCommand;
use mpba\Modules\Commands\ModuleMakeCommand;
use mpba\Modules\Commands\NotificationMakeCommand;
use mpba\Modules\Commands\PolicyMakeCommand;
use mpba\Modules\Commands\ProviderMakeCommand;
use mpba\Modules\Commands\PublishCommand;
use mpba\Modules\Commands\PublishConfigurationCommand;
use mpba\Modules\Commands\PublishMigrationCommand;
use mpba\Modules\Commands\PublishTranslationCommand;
use mpba\Modules\Commands\RequestMakeCommand;
use mpba\Modules\Commands\ResourceMakeCommand;
use mpba\Modules\Commands\RouteProviderMakeCommand;
use mpba\Modules\Commands\RuleMakeCommand;
use mpba\Modules\Commands\SeedCommand;
use mpba\Modules\Commands\SeedMakeCommand;
use mpba\Modules\Commands\SetupCommand;
use mpba\Modules\Commands\SupportMakeCommand;
use mpba\Modules\Commands\TestMakeCommand;
use mpba\Modules\Commands\TraitMakeCommand;
use mpba\Modules\Commands\UnUseCommand;
use mpba\Modules\Commands\UpdateCommand;
use mpba\Modules\Commands\UseCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [
        CommandMakeCommand::class,
        ControllerMakeCommand::class,
        DisableCommand::class,
        DumpCommand::class,
        EnableCommand::class,
        EventMakeCommand::class,
        HelperMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        MailMakeCommand::class,
        SupportMakeCommand::class,
        MiddlewareMakeCommand::class,
        NotificationMakeCommand::class,
        ProviderMakeCommand::class,
        RouteProviderMakeCommand::class,
        InstallCommand::class,
        ListCommand::class,
        ModuleDeleteCommand::class,
        ModuleMakeCommand::class,
        FactoryMakeCommand::class,
        PolicyMakeCommand::class,
        RequestMakeCommand::class,
        RuleMakeCommand::class,
        MigrateCommand::class,
        MigrateRefreshCommand::class,
        MigrateResetCommand::class,
        MigrateRollbackCommand::class,
        MigrateStatusCommand::class,
        MigrationMakeCommand::class,
        ModelMakeCommand::class,
        PublishCommand::class,
        PublishConfigurationCommand::class,
        PublishMigrationCommand::class,
        PublishTranslationCommand::class,
        SeedCommand::class,
        SeedMakeCommand::class,
        SetupCommand::class,
        UnUseCommand::class,
        UpdateCommand::class,
        UseCommand::class,
        ResourceMakeCommand::class,
        TestMakeCommand::class,
        ComponentMakeCommand::class,
        TraitMakeCommand::class,
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * @return array
     */
    public function provides()
    {
        $provides = $this->commands;

        return $provides;
    }
}

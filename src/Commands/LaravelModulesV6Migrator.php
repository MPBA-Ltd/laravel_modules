<?php

declare(strict_types=1);

namespace mpba\Modules\Commands;

use Illuminate\Console\Command;
use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\Module;

class LaravelModulesV6Migrator extends Command
{
    protected $name = 'module:v6:migrate';

    protected $description = 'Migrate laravel-modules v5 modules statuses to v6.';

    public function handle(): int
    {
        $moduleStatuses = [];
        /** @var mpba\Modules\Contracts\RepositoryInterface $modules */
        $modules = $this->laravel['modules'];

        $modules = $modules->all();
        /** @var Module $module */
        foreach ($modules as $module) {
            if ($module->json()->get('active') === 1) {
                $module->enable();
                $moduleStatuses[] = [$module->getName(), 'Enabled'];
            }
            if ($module->json()->get('active') === 0) {
                $module->disable();
                $moduleStatuses[] = [$module->getName(), 'Disabled'];
            }
        }
        $this->info('All modules have been migrated.');
        $this->table(['Module name', 'Status'], $moduleStatuses);

        return 0;
    }
}

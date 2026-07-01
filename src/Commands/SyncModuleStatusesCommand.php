<?php

declare(strict_types=1);

namespace mpba\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use mpba\Modules\DevelopmentSupport\Entities\ModuleStatus;


/**
 * Class SyncModuleStatusesCommand
 *
 * Scans the Modules directory and ensures each module has a record
 * in the module_statuses table.
 */
class SyncModuleStatusesCommand extends Command
{
    /**
     * The console command name and signature.
     *
     * @var string
     */
    protected $signature = 'module:sync-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan the Modules directory and add missing modules to the module_statuses table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $modulesPath = base_path('Modules');

        if (! File::exists($modulesPath)) {
            $this->error('Modules directory does not exist: ' . $modulesPath);

            return self::FAILURE;
        }

        /** @var Collection<int, string> $moduleNames */
        $moduleNames = $this->getModuleNames($modulesPath);

        if ($moduleNames->isEmpty()) {
            $this->warn('No modules were found in the Modules directory.');

            return self::SUCCESS;
        }

        $createdCount = 0;

        foreach ($moduleNames as $moduleName) {
            $moduleStatus = ModuleStatus::firstOrCreate(
                ['module' => $moduleName],
                ['enabled' => true]
            );

            if ($moduleStatus->wasRecentlyCreated) {
                $createdCount++;

                $this->line(sprintf('Added module: %s', $moduleName));
            } else {
                $this->line(sprintf('Already exists: %s', $moduleName));
            }
        }

        $this->info(sprintf(
            'Module sync complete. %d module(s) found, %d new record(s) added.',
            $moduleNames->count(),
            $createdCount
        ));

        return self::SUCCESS;
    }

    /**
     * Get all module directory names from the Modules path.
     *
     * @param string $modulesPath
     * @return Collection<int, string>
     */
    protected function getModuleNames(string $modulesPath): Collection
    {
        return collect(File::directories($modulesPath))
            ->map(static function (string $directory): string {
                return basename($directory);
            })
            ->filter(static function (string $moduleName): bool {
                return $moduleName !== '';
            })
            ->sort()
            ->values();
    }
}

<?php

declare(strict_types=1);

namespace mpba\Modules\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use mpba\Modules\DevelopmentSupport\Entities\ModuleStatus;

class ModuleStatusSeeder extends Seeder
{
    /**
     * Seed missing database module statuses from the existing file activator JSON.
     *
     * Existing database rows are left unchanged.
     */
    public function run(): void
    {
        foreach ($this->readStatuses() as $module => $enabled) {
            if (! is_string($module) || trim($module) === '') {
                continue;
            }

            ModuleStatus::query()->firstOrCreate(
                ['module' => trim($module)],
                ['enabled' => (bool) $enabled]
            );
        }
    }

    /**
     * @return array<string, bool>
     */
    private function readStatuses(): array
    {
        $path = $this->statusesFilePath();

        if ($path === null) {
            return [];
        }

        $statuses = json_decode(File::get($path), true);

        if (! is_array($statuses)) {
            return [];
        }

        return $statuses;
    }

    private function statusesFilePath(): ?string
    {
        $configuredPath = config('modules.activators.file.statuses-file');

        $paths = array_filter(array_unique([
            is_string($configuredPath) ? $configuredPath : null,
            base_path('modules_statuses.json'),
            base_path('module_statuses.json'),
            base_path('module_statues.json'),
        ]));

        foreach ($paths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}

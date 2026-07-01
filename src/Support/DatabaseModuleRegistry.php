<?php

namespace mpba\Modules\Support;

use Illuminate\Support\Collection;
use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\DevelopmentSupport\Entities\ModuleStatus;
use mpba\Modules\Module;

class DatabaseModuleRegistry
{
    public function __construct(
        protected RepositoryInterface $modules,
    ) {}

    public function all(): Collection
    {
        $this->syncMissingStatuses();

        $statuses = ModuleStatus::query()
            ->get()
            ->keyBy('module');

        return collect($this->modules->all())
            ->sortBy(fn (Module $module) => $module->get('order', 0))
            ->map(function (Module $module) use ($statuses): array {
                $status = $statuses->get($module->getName());

                return [
                    'name' => $module->getName(),
                    'alias' => $module->getAlias(),
                    'description' => $module->getDescription(),
                    'version' => $module->get('version'),
                    'order' => $module->get('order', 0),
                    'path' => $module->getPath(),
                    'requires' => $module->getRequires(),
                    'enabled' => (bool) ($status?->enabled ?? $module->isEnabled()),
                    'status_record' => $status,
                    'module' => $module,
                ];
            })
            ->values();
    }

    public function find(string $name): array
    {
        $this->syncMissingStatuses();

        /** @var Module $module */
        $module = $this->modules->findOrFail($name);
        $status = ModuleStatus::query()->firstOrCreate(
            ['module' => $module->getName()],
            ['enabled' => $module->isEnabled()]
        );

        return [
            'name' => $module->getName(),
            'alias' => $module->getAlias(),
            'description' => $module->getDescription(),
            'version' => $module->get('version'),
            'order' => $module->get('order', 0),
            'path' => $module->getPath(),
            'requires' => $module->getRequires(),
            'enabled' => (bool) $status->enabled,
            'status_record' => $status,
            'module' => $module,
        ];
    }

    public function enable(string $name): void
    {
        $module = $this->modules->findOrFail($name);

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            ['enabled' => true]
        );

        $this->modules->enable($module->getName());
    }

    public function disable(string $name): void
    {
        $module = $this->modules->findOrFail($name);

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            ['enabled' => false]
        );

        $this->modules->disable($module->getName());
    }

    public function syncMissingStatuses(): void
    {
        foreach ($this->modules->all() as $module) {
            ModuleStatus::query()->firstOrCreate(
                ['module' => $module->getName()],
                ['enabled' => $module->isEnabled()]
            );
        }
    }
}

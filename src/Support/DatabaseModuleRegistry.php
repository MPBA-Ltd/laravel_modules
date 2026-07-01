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
            ->map(function (Module $module) use ($statuses): array {
                return $this->mapModule($module, $statuses->get($module->getName()));
            })
            ->sortBy([
                fn (array $module) => $module['sort_order'],
                fn (array $module) => $module['name'],
            ])
            ->values();
    }

    public function find(string $name): array
    {
        $this->syncMissingStatuses();

        /** @var Module $module */
        $module = $this->modules->findOrFail($name);
        $status = ModuleStatus::query()->firstOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => $module->isEnabled(),
                'sort_order' => (int) $module->get('order', 0),
            ]
        );

        return $this->mapModule($module, $status);
    }

    public function update(string $name, array $data): void
    {
        $module = $this->modules->findOrFail($name);

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => ModuleStatus::query()
                    ->where('module', $module->getName())
                    ->value('enabled') ?? $module->isEnabled(),
                'description' => $data['description'] ?? null,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]
        );
    }

    public function enable(string $name): void
    {
        $module = $this->modules->findOrFail($name);

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => true,
                'sort_order' => (int) $module->get('order', 0),
            ]
        );

        $this->modules->enable($module->getName());
    }

    public function disable(string $name): void
    {
        $module = $this->modules->findOrFail($name);

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => false,
                'sort_order' => (int) $module->get('order', 0),
            ]
        );

        $this->modules->disable($module->getName());
    }

    public function syncMissingStatuses(): void
    {
        foreach ($this->modules->all() as $module) {
            ModuleStatus::query()->firstOrCreate(
                ['module' => $module->getName()],
                [
                    'enabled' => $module->isEnabled(),
                    'sort_order' => (int) $module->get('order', 0),
                ]
            );
        }
    }

    protected function mapModule(Module $module, ?ModuleStatus $status): array
    {
        return [
            'name' => $module->getName(),
            'alias' => $module->getAlias(),
            'description' => $status?->description,
            'disk_description' => $module->getDescription(),
            'version' => $this->moduleVersion($module),
            'sort_order' => (int) ($status?->sort_order ?? $module->get('order', 0)),
            'path' => $module->getPath(),
            'requires' => $module->getRequires(),
            'enabled' => (bool) ($status?->enabled ?? $module->isEnabled()),
            'status_record' => $status,
            'module' => $module,
        ];
    }

    protected function moduleVersion(Module $module): ?string
    {
        $version = $module->get('version');

        return is_string($version) && $version !== '' ? $version : null;
    }
}

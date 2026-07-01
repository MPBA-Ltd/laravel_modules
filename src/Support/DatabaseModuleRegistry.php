<?php

namespace mpba\Modules\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\DevelopmentSupport\Entities\ModuleStatus;
use mpba\Modules\Module;
use RuntimeException;

class DatabaseModuleRegistry
{
    public function __construct(
        protected RepositoryInterface $modules,
        protected Filesystem $files,
    ) {}

    public function all(): Collection
    {
        $this->syncMissingStatuses();

        $statuses = ModuleStatus::query()
            ->get()
            ->keyBy('module');

        $mapped = collect($this->modules->all())
            ->map(fn (Module $module): array => $this->mapModule($module, $statuses->get($module->getName())));

        return $mapped
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
                'version' => $this->moduleVersion($module),
                'description' => $this->safeDescription($module),
                'icon' => $this->defaultIcon($module),
                'category' => $this->defaultCategory($module),
                'vendor_type' => $this->defaultVendorType($module),
                'author' => $this->author($module),
                'sort_order' => (int) $module->get('order', 0),
            ]
        );

        return $this->mapModule($module, $status, true);
    }

    public function update(string $name, array $data): void
    {
        $module = $this->modules->findOrFail($name);
        $existing = ModuleStatus::query()->firstWhere('module', $module->getName());

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => $existing?->enabled ?? $module->isEnabled(),
                'description' => $data['description'] ?? null,
                'version' => $data['version'] ?? null,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
                'icon' => $data['icon'] ?? null,
                'category' => $data['category'] ?? null,
                'vendor_type' => $data['vendor_type'] ?? null,
                'author' => $data['author'] ?? null,
                'notes' => $data['notes'] ?? null,
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
                'description' => ModuleStatus::query()->where('module', $module->getName())->value('description') ?? $this->safeDescription($module),
                'version' => ModuleStatus::query()->where('module', $module->getName())->value('version') ?? $this->moduleVersion($module),
                'icon' => ModuleStatus::query()->where('module', $module->getName())->value('icon') ?? $this->defaultIcon($module),
                'category' => ModuleStatus::query()->where('module', $module->getName())->value('category') ?? $this->defaultCategory($module),
                'vendor_type' => ModuleStatus::query()->where('module', $module->getName())->value('vendor_type') ?? $this->defaultVendorType($module),
                'author' => ModuleStatus::query()->where('module', $module->getName())->value('author') ?? $this->author($module),
                'sort_order' => ModuleStatus::query()->where('module', $module->getName())->value('sort_order') ?? (int) $module->get('order', 0),
            ]
        );

        $this->modules->enable($module->getName());
    }

    public function disable(string $name): void
    {
        $module = $this->modules->findOrFail($name);
        $blocking = $this->enabledDependents($module->getName());

        if ($blocking !== []) {
            throw new RuntimeException($module->getName().' cannot be disabled because it is required by: '.implode(', ', $blocking).'.');
        }

        ModuleStatus::query()->updateOrCreate(
            ['module' => $module->getName()],
            [
                'enabled' => false,
                'sort_order' => ModuleStatus::query()->where('module', $module->getName())->value('sort_order') ?? (int) $module->get('order', 0),
            ]
        );

        $this->modules->disable($module->getName());
    }

    public function bulk(string $action, array $names): array
    {
        $changed = 0;
        $blocked = [];

        foreach (array_unique(array_filter($names)) as $name) {
            try {
                if ($action === 'enable') {
                    $this->enable($name);
                    $changed++;
                } elseif ($action === 'disable') {
                    $this->disable($name);
                    $changed++;
                }
            } catch (RuntimeException $exception) {
                $blocked[] = $exception->getMessage();
            }
        }

        return ['changed' => $changed, 'blocked' => $blocked];
    }

    public function syncMissingStatuses(): void
    {
        foreach ($this->modules->all() as $module) {
            ModuleStatus::query()->firstOrCreate(
                ['module' => $module->getName()],
                [
                    'enabled' => $module->isEnabled(),
                    'description' => $this->safeDescription($module),
                    'version' => $this->moduleVersion($module),
                    'icon' => $this->defaultIcon($module),
                    'category' => $this->defaultCategory($module),
                    'vendor_type' => $this->defaultVendorType($module),
                    'author' => $this->author($module),
                    'sort_order' => (int) $module->get('order', 0),
                ]
            );
        }
    }

    protected function mapModule(Module $module, ?ModuleStatus $status, bool $withDetails = false): array
    {
        $requires = $this->safeRequires($module);
        $diskVersion = $this->moduleVersion($module);
        $databaseVersion = $this->databaseVersion($status);
        $composer = $this->composerData($module);
        $missingDependencies = $this->missingDependencies($requires);
        $enabledDependents = $this->enabledDependents($module->getName());

        $mapped = [
            'name' => $module->getName(),
            'alias' => $this->safeString($module->get('alias', Str::kebab($module->getName()))),
            'description' => $this->databaseDescription($status) ?? $this->safeDescription($module),
            'database_description' => $this->databaseDescription($status),
            'disk_description' => $this->safeDescription($module),
            'version' => $databaseVersion ?? $diskVersion,
            'database_version' => $databaseVersion,
            'disk_version' => $diskVersion,
            'has_update' => $this->hasUpdate($databaseVersion, $diskVersion),
            'icon' => $this->databaseString($status?->icon) ?? $this->defaultIcon($module),
            'category' => $this->databaseString($status?->category) ?? $this->defaultCategory($module),
            'vendor_type' => $this->databaseString($status?->vendor_type) ?? $this->defaultVendorType($module),
            'author' => $this->databaseString($status?->author) ?? $this->author($module),
            'notes' => $status?->notes,
            'sort_order' => (int) ($status?->sort_order ?? $module->get('order', 0)),
            'path' => $module->getPath(),
            'requires' => $requires,
            'missing_dependencies' => $missingDependencies,
            'has_missing_dependencies' => $missingDependencies !== [],
            'enabled_dependents' => $enabledDependents,
            'can_disable' => $enabledDependents === [],
            'enabled' => (bool) ($status?->enabled ?? $module->isEnabled()),
            'status_record' => $status,
            'updated_at' => $status?->updated_at,
            'composer_name' => $composer['name'] ?? null,
            'composer_description' => $composer['description'] ?? null,
            'namespace' => $this->namespace($module),
            'git_commit' => $this->gitCommit($module),
            'build_date' => $this->buildDate($module),
            'module' => $module,
        ];

        if ($withDetails) {
            $mapped += $this->inspect($module);
        }

        return $mapped;
    }

    protected function inspect(Module $module): array
    {
        $path = $module->getPath();

        return [
            'readme' => $this->readFirstExisting($path, ['README.md', 'readme.md'], 7000),
            'changelog' => $this->readFirstExisting($path, ['CHANGELOG.md', 'changelog.md', 'changelog.yaml'], 7000),
            'routes' => $this->filesUnder($path, ['Routes/*.php', 'routes/*.php']),
            'migrations' => $this->filesUnder($path, ['Database/Migrations/*.php', 'database/migrations/*.php']),
            'models' => $this->filesUnder($path, ['app/Models/*.php', 'Models/*.php', 'Entities/*.php', 'app/Entities/*.php']),
            'controllers' => $this->filesUnder($path, ['app/Http/Controllers/*.php', 'Http/Controllers/*.php']),
            'providers' => $this->filesUnder($path, ['app/Providers/*.php', 'Providers/*.php']),
        ];
    }

    protected function filesUnder(string $path, array $patterns): array
    {
        $files = [];

        foreach ($patterns as $pattern) {
            foreach ($this->files->glob($path.'/'.$pattern) ?: [] as $file) {
                $files[] = str_replace($path.'/', '', $file);
            }
        }

        return array_values(array_unique($files));
    }

    protected function readFirstExisting(string $path, array $files, int $limit): ?string
    {
        foreach ($files as $file) {
            $candidate = $path.'/'.$file;

            if ($this->files->exists($candidate)) {
                return Str::limit($this->files->get($candidate), $limit, "\n...");
            }
        }

        return null;
    }

    protected function enabledDependents(string $name): array
    {
        $dependents = [];
        $statuses = ModuleStatus::query()->get()->keyBy('module');

        foreach ($this->modules->all() as $candidate) {
            if ($candidate->getName() === $name) {
                continue;
            }

            $status = $statuses->get($candidate->getName());
            $enabled = (bool) ($status?->enabled ?? $candidate->isEnabled());

            if ($enabled && in_array($name, $this->safeRequires($candidate), true)) {
                $dependents[] = $candidate->getName();
            }
        }

        sort($dependents);

        return $dependents;
    }

    protected function missingDependencies(array $requires): array
    {
        if ($requires === []) {
            return [];
        }

        $installed = collect($this->modules->all())
            ->map(fn (Module $module): string => $module->getName())
            ->all();

        return array_values(array_diff($requires, $installed));
    }

    protected function hasUpdate(?string $databaseVersion, ?string $diskVersion): bool
    {
        if (! $databaseVersion || ! $diskVersion) {
            return false;
        }

        return version_compare($diskVersion, $databaseVersion, '>');
    }

    protected function databaseVersion(?ModuleStatus $status): ?string
    {
        return $this->databaseString($status?->version);
    }

    protected function databaseDescription(?ModuleStatus $status): ?string
    {
        return $this->databaseString($status?->description);
    }

    protected function moduleVersion(Module $module): ?string
    {
        $version = $module->get('version');

        return is_string($version) && trim($version) !== '' ? trim($version) : null;
    }

    protected function safeDescription(Module $module): ?string
    {
        $description = $module->get('description');

        return is_string($description) && trim($description) !== '' ? trim($description) : null;
    }

    protected function safeRequires(Module $module): array
    {
        $requires = $module->get('requires', []);

        return is_array($requires) ? array_values(array_filter($requires)) : [];
    }

    protected function defaultIcon(Module $module): string
    {
        $icons = [
            'Analytics' => 'fa-chart-line',
            'Audit' => 'fa-clipboard-check',
            'AuditTrail' => 'fa-clipboard-list',
            'CarrierManagement' => 'fa-route',
            'ChatGPT' => 'fa-robot',
            'Configuration' => 'fa-sliders',
            'Countries' => 'fa-globe',
            'Documentation' => 'fa-book-open',
            'Helpdesk' => 'fa-headset',
            'InventoryManagement' => 'fa-boxes-stacked',
            'Platform' => 'fa-building-shield',
            'Pricing' => 'fa-tags',
            'Security' => 'fa-user-shield',
            'Warehouse' => 'fa-warehouse',
        ];

        return $icons[$module->getName()] ?? 'fa-puzzle-piece';
    }

    protected function defaultCategory(Module $module): string
    {
        return in_array($module->getName(), ['Platform', 'Security', 'Configuration', 'InternalApi'], true) ? 'Core' : 'Optional';
    }

    protected function defaultVendorType(Module $module): string
    {
        $composer = $this->composerData($module);
        $name = $composer['name'] ?? '';

        return Str::startsWith($name, 'mpba/') || $name === '' ? 'MPBA' : 'Third-party';
    }

    protected function author(Module $module): ?string
    {
        $authors = $this->composerData($module)['authors'] ?? null;

        if (is_array($authors) && isset($authors[0]['name'])) {
            return $authors[0]['name'];
        }

        return 'MPBA';
    }

    protected function namespace(Module $module): ?string
    {
        $autoload = $this->composerData($module)['autoload']['psr-4'] ?? null;

        if (is_array($autoload)) {
            return array_key_first($autoload);
        }

        return 'Modules\\'.$module->getName().'\\';
    }

    protected function composerData(Module $module): array
    {
        $path = $module->getPath().'/composer.json';

        if (! $this->files->exists($path)) {
            return [];
        }

        $data = json_decode($this->files->get($path), true);

        return is_array($data) ? $data : [];
    }

    protected function gitCommit(Module $module): ?string
    {
        $head = base_path('.git/HEAD');

        if (! $this->files->exists($head)) {
            return null;
        }

        $content = trim($this->files->get($head));

        if (Str::startsWith($content, 'ref: ')) {
            $ref = trim(Str::after($content, 'ref: '));
            $refPath = base_path('.git/'.$ref);

            if ($this->files->exists($refPath)) {
                return substr(trim($this->files->get($refPath)), 0, 12);
            }
        }

        return substr($content, 0, 12) ?: null;
    }

    protected function buildDate(Module $module): ?string
    {
        $path = $module->getPath().'/module.json';

        return $this->files->exists($path)
            ? date('Y-m-d H:i', $this->files->lastModified($path))
            : null;
    }

    protected function databaseString(?string $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    protected function safeString(?string $value): string
    {
        return is_string($value) ? $value : '';
    }
}

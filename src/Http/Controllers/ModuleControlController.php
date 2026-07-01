<?php

namespace mpba\Modules\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use mpba\Modules\Support\DatabaseModuleRegistry;
use RuntimeException;

class ModuleControlController extends Controller
{
    public function __construct(
        protected DatabaseModuleRegistry $registry,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $filter = (string) $request->query('filter', $request->query('status', 'all'));
        $filter = in_array($filter, ['all', 'enabled', 'disabled', 'core', 'optional', 'mpba', 'third-party', 'updates', 'missing-dependencies'], true) ? $filter : 'all';
        $sort = (string) $request->query('sort', 'order');
        $sort = in_array($sort, ['order', 'name', 'status', 'version', 'updated'], true) ? $sort : 'order';
        $perPage = 4;
        $page = max(1, (int) $request->query('page', 1));

        $allModules = $this->registry->all();
        $filteredModules = $this->applyFilter($allModules, $filter);

        if ($search !== '') {
            $filteredModules = $filteredModules->filter(function (array $module) use ($search): bool {
                $haystack = implode(' ', array_filter([
                    $module['name'] ?? null,
                    $module['alias'] ?? null,
                    $module['description'] ?? null,
                    $module['disk_description'] ?? null,
                    $module['version'] ?? null,
                    $module['database_version'] ?? null,
                    $module['disk_version'] ?? null,
                    $module['category'] ?? null,
                    $module['vendor_type'] ?? null,
                    $module['author'] ?? null,
                    $module['composer_name'] ?? null,
                    $module['path'] ?? null,
                ]));

                return Str::contains(Str::lower($haystack), Str::lower($search));
            })->values();
        }

        $filteredModules = $this->applySort($filteredModules, $sort);

        $modules = new LengthAwarePaginator(
            $filteredModules->forPage($page, $perPage)->values(),
            $filteredModules->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('modules-control::index', [
            'modules' => $modules,
            'allModules' => $allModules,
            'filteredCount' => $filteredModules->count(),
            'search' => $search,
            'filter' => $filter,
            'status' => $filter,
            'sort' => $sort,
            'stats' => $this->stats($allModules),
        ]);
    }

    public function show(string $module): View
    {
        return view('modules-control::show', [
            'module' => $this->registry->find($module),
        ]);
    }

    public function update(Request $request, string $module): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['nullable', 'string', 'max:2000'],
            'version' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'icon' => ['nullable', 'string', 'max:80'],
            'category' => ['nullable', 'string', 'max:80'],
            'vendor_type' => ['nullable', 'string', 'max:80'],
            'author' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->registry->update($module, $data);

        return redirect()
            ->route('modules.control.show', $module)
            ->with('status', "{$module} database metadata saved.");
    }

    public function sync(): RedirectResponse
    {
        $this->registry->syncMissingStatuses();

        return redirect()
            ->route('modules.control.index')
            ->with('status', 'Module database statuses synced.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:enable,disable'],
            'modules' => ['array'],
            'modules.*' => ['string'],
        ]);

        $result = $this->registry->bulk($data['action'], $data['modules'] ?? []);
        $message = $result['changed'].' module'.($result['changed'] === 1 ? '' : 's').' updated.';

        if ($result['blocked'] !== []) {
            return back()
                ->with('warning', $message.' Some modules were blocked: '.implode(' ', $result['blocked']));
        }

        return back()->with('status', $message);
    }

    public function enable(string $module): RedirectResponse
    {
        $this->registry->enable($module);

        return back()->with('status', "{$module} enabled in the database.");
    }

    public function disable(string $module): RedirectResponse
    {
        try {
            $this->registry->disable($module);
        } catch (RuntimeException $exception) {
            return back()->with('warning', $exception->getMessage());
        }

        return back()->with('status', "{$module} disabled in the database.");
    }

    protected function applyFilter($modules, string $filter)
    {
        return match ($filter) {
            'enabled' => $modules->where('enabled', true)->values(),
            'disabled' => $modules->where('enabled', false)->values(),
            'core' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['category'], 'Core') === 0)->values(),
            'optional' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['category'], 'Optional') === 0)->values(),
            'mpba' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['vendor_type'], 'MPBA') === 0)->values(),
            'third-party' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['vendor_type'], 'Third-party') === 0)->values(),
            'updates' => $modules->where('has_update', true)->values(),
            'missing-dependencies' => $modules->where('has_missing_dependencies', true)->values(),
            default => $modules->values(),
        };
    }

    protected function applySort($modules, string $sort)
    {
        return match ($sort) {
            'name' => $modules->sortBy('name')->values(),
            'status' => $modules->sortByDesc('enabled')->values(),
            'version' => $modules->sortBy('version')->values(),
            'updated' => $modules->sortByDesc(fn (array $module) => $module['updated_at']?->timestamp ?? 0)->values(),
            default => $modules->sortBy([
                fn (array $module) => $module['sort_order'],
                fn (array $module) => $module['name'],
            ])->values(),
        };
    }

    protected function stats($modules): array
    {
        return [
            'installed' => $modules->count(),
            'enabled' => $modules->where('enabled', true)->count(),
            'disabled' => $modules->where('enabled', false)->count(),
            'updates' => $modules->where('has_update', true)->count(),
            'missing' => $modules->where('has_missing_dependencies', true)->count(),
            'core' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['category'], 'Core') === 0)->count(),
            'optional' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['category'], 'Optional') === 0)->count(),
            'mpba' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['vendor_type'], 'MPBA') === 0)->count(),
            'third_party' => $modules->filter(fn (array $module): bool => strcasecmp((string) $module['vendor_type'], 'Third-party') === 0)->count(),
        ];
    }
}

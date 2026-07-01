<x-layouts.app :title="__('Module Control')">
    <x-dynamic-component
        :component="config('modules.admin.hero_component', 'wms.page-hero')"
        title="Module Control"
        subtitle="Manage installed modules, database metadata, dependencies and activation state."
    />

    @php
        $filterCards = [
            ['key' => 'all', 'label' => 'Installed', 'value' => $stats['installed'], 'icon' => 'fa-puzzle-piece', 'tone' => 'blue', 'hint' => 'All modules'],
            ['key' => 'enabled', 'label' => 'Enabled', 'value' => $stats['enabled'], 'icon' => 'fa-circle-check', 'tone' => 'emerald', 'hint' => 'Active modules'],
            ['key' => 'disabled', 'label' => 'Disabled', 'value' => $stats['disabled'], 'icon' => 'fa-circle-pause', 'tone' => 'red', 'hint' => 'Inactive modules'],
            ['key' => 'updates', 'label' => 'Updates Available', 'value' => $stats['updates'], 'icon' => 'fa-arrow-up-from-bracket', 'tone' => 'amber', 'hint' => 'Disk version newer'],
            ['key' => 'missing-dependencies', 'label' => 'Dependencies Missing', 'value' => $stats['missing'], 'icon' => 'fa-triangle-exclamation', 'tone' => 'orange', 'hint' => 'Requires attention'],
        ];

        $secondaryFilters = [
            'core' => 'Core',
            'optional' => 'Optional',
            'mpba' => 'Installed by MPBA',
            'third-party' => 'Third-party',
        ];

        $toneClasses = [
            'blue' => 'border-blue-500/40 bg-blue-500/10 text-blue-700 dark:text-blue-300',
            'emerald' => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300',
            'red' => 'border-red-500/40 bg-red-500/10 text-red-700 dark:text-red-300',
            'amber' => 'border-amber-500/40 bg-amber-500/10 text-amber-700 dark:text-amber-300',
            'orange' => 'border-orange-500/40 bg-orange-500/10 text-orange-700 dark:text-orange-300',
        ];
    @endphp

    <div class="space-y-6">
        @foreach (['status' => 'emerald', 'warning' => 'amber', 'error' => 'red'] as $flash => $tone)
            @if (session($flash))
                <div class="rounded-2xl border px-4 py-3 text-sm font-medium {{ $toneClasses[$tone] ?? $toneClasses['blue'] }}">
                    {{ session($flash) }}
                </div>
            @endif
        @endforeach

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($filterCards as $card)
                <a
                    href="{{ route('modules.control.index', array_filter(['filter' => $card['key'] === 'all' ? null : $card['key'], 'search' => $search, 'sort' => $sort === 'order' ? null : $sort])) }}"
                    class="group rounded-2xl border p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $filter === $card['key'] ? ($toneClasses[$card['tone']] ?? $toneClasses['blue']) : 'border-zinc-200 bg-white text-zinc-900 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</div>
                            <div class="mt-2 text-3xl font-bold">{{ $card['value'] }}</div>
                            <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $card['hint'] }}</div>
                        </div>
                        <span class="rounded-xl border border-zinc-200 bg-white/70 p-3 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-950/60 dark:text-zinc-300">
                            <i class="fa-solid {{ $card['icon'] }}" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Installed modules</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Descriptions, versions, display metadata and sort order are stored in the database only.
                    </p>
                </div>

                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <form method="GET" action="{{ route('modules.control.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        @if ($filter !== 'all')
                            <input type="hidden" name="filter" value="{{ $filter }}" />
                        @endif
                        <input
                            type="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search name, description, version, author..."
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500 sm:w-80"
                        />
                        <select
                            name="sort"
                            class="rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                            <option value="order" @selected($sort === 'order')>Sort: Order</option>
                            <option value="name" @selected($sort === 'name')>Sort: Name</option>
                            <option value="status" @selected($sort === 'status')>Sort: Status</option>
                            <option value="version" @selected($sort === 'version')>Sort: Version</option>
                            <option value="updated" @selected($sort === 'updated')>Sort: Last updated</option>
                        </select>
                        <button type="submit" class="rounded-xl border border-blue-500/40 bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                            Search
                        </button>
                    </form>

                    <form method="POST" action="{{ route('modules.control.sync') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                            Sync Database
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($secondaryFilters as $key => $label)
                    <a
                        href="{{ route('modules.control.index', array_filter(['filter' => $key, 'search' => $search, 'sort' => $sort === 'order' ? null : $sort])) }}"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition {{ $filter === $key ? 'border-blue-500/40 bg-blue-500/10 text-blue-700 dark:text-blue-300' : 'border-zinc-300 text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach

                @if ($filter !== 'all' || $search !== '')
                    <a href="{{ route('modules.control.index') }}" class="rounded-full border border-zinc-300 px-3 py-1 text-xs font-semibold text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800">
                        Clear filters
                    </a>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-1 text-sm text-zinc-500 dark:text-zinc-400 sm:flex-row sm:items-center sm:justify-between">
            <div>
                Showing {{ $modules->firstItem() ?? 0 }}-{{ $modules->lastItem() ?? 0 }} of {{ $modules->total() }} modules.
                @if ($search !== '')
                    Search: <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $search }}</span>
                @endif
            </div>
            <div>4 modules per page</div>
        </div>

        <form method="POST" action="{{ route('modules.control.bulk') }}" class="space-y-4">
            @csrf

            <div class="flex flex-col gap-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-zinc-600 dark:text-zinc-400">
                    Select modules on this page and apply a bulk action.
                </div>
                <div class="flex flex-wrap gap-2">
                    <select name="action" class="rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                        <option value="enable">Enable selected</option>
                        <option value="disable">Disable selected</option>
                    </select>
                    <button type="submit" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                        Apply
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-950/70">
                            <tr>
                                <th class="w-10 px-4 py-3 text-left"><span class="sr-only">Select</span></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Module</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Version</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Last Updated</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($modules as $module)
                                <tr class="align-top bg-transparent text-zinc-900 transition hover:!bg-zinc-100/70 dark:text-zinc-100 dark:hover:!bg-zinc-800/40">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="modules[]" value="{{ $module['name'] }}" class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-950" />
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-3">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200">
                                                <i class="fa-solid {{ $module['icon'] ?: 'fa-puzzle-piece' }}" aria-hidden="true"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $module['name'] }}</div>
                                                <div class="mt-1 text-xs text-zinc-500">{{ $module['alias'] }}</div>
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    <span class="rounded-full border border-zinc-300 px-2 py-0.5 text-[11px] font-semibold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">{{ $module['category'] }}</span>
                                                    <span class="rounded-full border border-zinc-300 px-2 py-0.5 text-[11px] font-semibold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">{{ $module['vendor_type'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="max-w-md px-4 py-4">
                                        <p class="line-clamp-3 text-sm leading-6 text-zinc-700 dark:text-zinc-300">
                                            {{ $module['description'] ?: 'No database description saved.' }}
                                        </p>
                                        @if ($module['has_missing_dependencies'])
                                            <div class="mt-2 rounded-lg border border-orange-500/30 bg-orange-500/10 px-2 py-1 text-xs font-semibold text-orange-700 dark:text-orange-300">
                                                Missing: {{ implode(', ', $module['missing_dependencies']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                                        <div>{{ $module['version'] ?: '—' }}</div>
                                        @if ($module['has_update'])
                                            <span class="mt-1 inline-flex rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:text-amber-300">Update available</span>
                                        @elseif ($module['database_version'])
                                            <span class="mt-1 inline-flex text-xs text-blue-600 dark:text-blue-300">Database</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($module['enabled'])
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                                <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Enabled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-300">
                                                <i class="fa-solid fa-circle-pause" aria-hidden="true"></i> Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $module['updated_at']?->format('d M Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('modules.control.show', $module['name']) }}" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm font-semibold text-zinc-800 hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-100 dark:hover:!bg-zinc-800">View</a>
                                            @if ($module['enabled'])
                                                <button type="submit" formaction="{{ route('modules.control.disable', $module['name']) }}" class="rounded-lg border border-red-500/40 bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700" @disabled(! $module['can_disable'])>
                                                    Disable
                                                </button>
                                            @else
                                                <button type="submit" formaction="{{ route('modules.control.enable', $module['name']) }}" class="rounded-lg border border-emerald-500/40 bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700">
                                                    Enable
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border border-zinc-200 bg-zinc-50 text-zinc-500 dark:border-zinc-800 dark:bg-zinc-950">
                                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                        </div>
                                        <div class="mt-3 font-semibold text-zinc-900 dark:text-zinc-100">No modules found</div>
                                        <div class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Adjust the search or filter and try again.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div>
            {{ $modules->links() }}
        </div>
    </div>
</x-layouts.app>

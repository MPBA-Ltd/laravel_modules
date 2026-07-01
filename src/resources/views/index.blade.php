<x-layouts.app :title="__('Module Control')">
    @php
        $installedCount = (int) ($stats['installed'] ?? 0);
        $enabledCount = (int) ($stats['enabled'] ?? 0);
        $disabledCount = (int) ($stats['disabled'] ?? 0);

        $filterCards = [
            ['key' => 'all', 'label' => 'Installed', 'value' => $installedCount, 'icon' => 'fa-puzzle-piece', 'tone' => 'blue', 'hint' => 'All modules'],
            ['key' => 'enabled', 'label' => 'Enabled', 'value' => $enabledCount, 'icon' => 'fa-circle-check', 'tone' => 'emerald', 'hint' => 'Active modules'],
            ['key' => 'disabled', 'label' => 'Disabled', 'value' => $disabledCount, 'icon' => 'fa-circle-pause', 'tone' => 'red', 'hint' => 'Inactive modules'],
            ['key' => 'updates', 'label' => 'Updates Available', 'value' => $stats['updates'] ?? 0, 'icon' => 'fa-arrow-up-from-bracket', 'tone' => 'sky', 'hint' => 'Disk version newer'],
            ['key' => 'missing-dependencies', 'label' => 'Dependencies Missing', 'value' => $stats['missing'] ?? 0, 'icon' => 'fa-triangle-exclamation', 'tone' => 'amber', 'hint' => 'Requires attention'],
        ];

        $secondaryFilters = [
            'core' => 'Core',
            'optional' => 'Optional',
            'mpba' => 'Installed by MPBA',
            'third-party' => 'Third-party',
        ];

        $cardTone = [
            'blue' => 'border-blue-400/70 bg-blue-500/10 text-blue-300',
            'emerald' => 'border-emerald-400/60 bg-emerald-500/10 text-emerald-300',
            'red' => 'border-red-400/60 bg-red-500/10 text-red-300',
            'sky' => 'border-sky-400/60 bg-sky-500/10 text-sky-300',
            'amber' => 'border-amber-400/60 bg-amber-500/10 text-amber-300',
        ];
    @endphp

    <style>
        .mc-row,
        .mc-row:hover,
        .mc-row:focus-within {
            background: rgb(24 24 27 / 0.68) !important;
            color: inherit !important;
        }

        .mc-row:hover {
            background: linear-gradient(90deg, rgb(24 24 27 / 0.92), rgb(9 24 43 / 0.78)) !important;
        }

        .mc-view-button,
        .mc-view-button:hover,
        .mc-view-button:focus {
            background: rgb(5 150 105) !important;
            border-color: rgb(16 185 129 / 0.65) !important;
            color: #fff !important;
        }

        .mc-view-button:hover {
            background: rgb(4 120 87) !important;
        }
    </style>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-[1.35rem] border border-blue-400/20 bg-[radial-gradient(circle_at_30%_15%,rgba(22,163,184,0.25),transparent_32%),linear-gradient(135deg,#050b21_0%,#0b1a47_48%,#07111f_100%)] px-8 py-9 shadow-2xl shadow-black/30 sm:px-10 lg:px-12">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(30rem,36rem)] lg:items-start">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-emerald-300">Platform Administration</p>
                    <h1 class="mt-6 text-4xl font-black tracking-tight text-white sm:text-5xl">Module Control</h1>
                    <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-slate-200">
                        Manage installed modules, database metadata, dependencies and activation state.
                    </p>
                </div>

                <div class="rounded-[1.35rem] border border-white/20 bg-slate-950/25 p-6 shadow-2xl shadow-black/20 ring-1 ring-white/10 backdrop-blur">
                    <div class="flex items-start justify-between gap-4">
                        <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-emerald-300/70 bg-emerald-400/10 text-emerald-200 shadow-sm">
                            <i class="fa-solid fa-puzzle-piece text-3xl" aria-hidden="true"></i>
                        </span>

                        <span class="inline-flex items-center rounded-full border border-white/25 bg-white/10 px-5 py-2 text-xs font-black uppercase tracking-[0.22em] text-slate-100">
                            Database backed
                        </span>
                    </div>

                    <div class="mt-8 grid grid-cols-3 gap-0">
                        <div class="border-r border-white/20 pr-6">
                            <div class="text-4xl font-black text-white">{{ $installedCount }}</div>
                            <div class="mt-1 text-sm font-black uppercase tracking-wide text-slate-200">Installed</div>
                        </div>
                        <div class="border-r border-white/20 px-6">
                            <div class="text-4xl font-black text-emerald-300">{{ $enabledCount }}</div>
                            <div class="mt-1 text-sm font-black uppercase tracking-wide text-slate-200">Enabled</div>
                        </div>
                        <div class="pl-6">
                            <div class="text-4xl font-black text-red-300">{{ $disabledCount }}</div>
                            <div class="mt-1 text-sm font-black uppercase tracking-wide text-slate-200">Disabled</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @foreach (['status' => 'emerald', 'warning' => 'amber', 'error' => 'red'] as $flash => $tone)
            @if (session($flash))
                <div class="rounded-2xl border border-{{ $tone }}-500/40 bg-{{ $tone }}-500/10 px-4 py-3 text-sm font-medium text-{{ $tone }}-300">
                    {{ session($flash) }}
                </div>
            @endif
        @endforeach

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ($filterCards as $card)
                <a
                    href="{{ route('modules.control.index', array_filter(['filter' => $card['key'] === 'all' ? null : $card['key'], 'search' => $search, 'sort' => $sort === 'order' ? null : $sort])) }}"
                    class="group rounded-2xl border p-5 shadow-sm transition hover:-translate-y-0.5 hover:bg-zinc-900/90 hover:shadow-md {{ $filter === $card['key'] ? ($cardTone[$card['tone']] ?? $cardTone['blue']) : 'border-zinc-800 bg-zinc-900 text-zinc-100' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs font-black uppercase tracking-wide text-zinc-400">{{ $card['label'] }}</div>
                            <div class="mt-2 text-4xl font-black {{ $card['tone'] === 'emerald' ? 'text-emerald-300' : ($card['tone'] === 'red' ? 'text-red-300' : ($card['tone'] === 'amber' ? 'text-amber-300' : 'text-blue-300')) }}">{{ $card['value'] }}</div>
                            <div class="mt-1 text-sm text-zinc-400">{{ $card['hint'] }}</div>
                        </div>
                        <span class="rounded-xl border border-zinc-700 bg-zinc-950/70 p-3 text-zinc-200">
                            <i class="fa-solid {{ $card['icon'] }}" aria-hidden="true"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-zinc-100">Installed modules</h2>
                    <p class="mt-1 text-sm text-zinc-400">
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
                            class="w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 placeholder:text-zinc-500 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 sm:w-80"
                        />
                        <select
                            name="sort"
                            class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                        >
                            <option value="order" @selected($sort === 'order')>Sort: Order</option>
                            <option value="name" @selected($sort === 'name')>Sort: Name</option>
                            <option value="status" @selected($sort === 'status')>Sort: Status</option>
                            <option value="version" @selected($sort === 'version')>Sort: Version</option>
                            <option value="updated" @selected($sort === 'updated')>Sort: Last updated</option>
                        </select>
                        <button type="submit" class="rounded-xl border border-emerald-500/40 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            Search
                        </button>
                    </form>

                    <form method="POST" action="{{ route('modules.control.sync') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-zinc-100 hover:bg-zinc-800">
                            Sync Database
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($secondaryFilters as $key => $label)
                    <a
                        href="{{ route('modules.control.index', array_filter(['filter' => $key, 'search' => $search, 'sort' => $sort === 'order' ? null : $sort])) }}"
                        class="rounded-full border px-3 py-1 text-xs font-semibold transition {{ $filter === $key ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-zinc-700 text-zinc-300 hover:bg-zinc-800' }}"
                    >
                        {{ $label }}
                    </a>
                @endforeach

                @if ($filter !== 'all' || $search !== '')
                    <a href="{{ route('modules.control.index') }}" class="rounded-full border border-zinc-700 px-3 py-1 text-xs font-semibold text-zinc-300 hover:bg-zinc-800">
                        Clear filters
                    </a>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-1 text-sm text-zinc-400 sm:flex-row sm:items-center sm:justify-between">
            <div>
                Showing {{ $modules->firstItem() ?? 0 }}-{{ $modules->lastItem() ?? 0 }} of {{ $modules->total() }} modules.
                @if ($search !== '')
                    Search: <span class="font-semibold text-zinc-200">{{ $search }}</span>
                @endif
            </div>
            <div>4 modules per page</div>
        </div>

        <form method="POST" action="{{ route('modules.control.bulk') }}" class="space-y-4">
            @csrf

            <div class="flex flex-col gap-3 rounded-2xl border border-zinc-800 bg-zinc-900 p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-zinc-400">
                    Select modules on this page and apply a bulk action.
                </div>
                <div class="flex flex-wrap gap-2">
                    <select name="action" class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100">
                        <option value="enable">Enable selected</option>
                        <option value="disable">Disable selected</option>
                    </select>
                    <button type="submit" class="rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm font-semibold text-zinc-100 hover:bg-zinc-800">
                        Apply
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800">
                        <thead class="bg-zinc-950/70">
                            <tr>
                                <th class="w-12 px-4 py-3 text-left"><span class="sr-only">Select</span></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Module</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Description</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Version</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500">Last Updated</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-800">
                            @forelse ($modules as $module)
                                <tr class="mc-row align-top transition">
                                    <td class="px-4 py-4">
                                        <input type="checkbox" name="modules[]" value="{{ $module['name'] }}" class="rounded border-zinc-700 bg-zinc-950 text-emerald-600 focus:ring-emerald-500" />
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex gap-3">
                                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-950 text-zinc-200">
                                                <i class="fa-solid {{ $module['icon'] ?: 'fa-puzzle-piece' }}" aria-hidden="true"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-zinc-100">{{ $module['name'] }}</div>
                                                <div class="mt-1 text-xs text-zinc-500">{{ $module['alias'] }}</div>
                                                <div class="mt-2 flex flex-wrap gap-1.5">
                                                    <span class="rounded-full border border-zinc-700 px-2 py-0.5 text-[11px] font-semibold text-zinc-300">{{ $module['category'] }}</span>
                                                    <span class="rounded-full border border-zinc-700 px-2 py-0.5 text-[11px] font-semibold text-zinc-300">{{ $module['vendor_type'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="max-w-md px-4 py-4">
                                        <p class="line-clamp-3 text-sm leading-6 text-zinc-300">
                                            {{ $module['description'] ?: 'No database description saved.' }}
                                        </p>
                                        @if ($module['has_missing_dependencies'])
                                            <div class="mt-2 rounded-lg border border-orange-500/30 bg-orange-500/10 px-2 py-1 text-xs font-semibold text-orange-300">
                                                Missing: {{ implode(', ', $module['missing_dependencies']) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-zinc-300">
                                        <div>{{ $module['version'] ?: '—' }}</div>
                                        @if ($module['has_update'])
                                            <span class="mt-1 inline-flex rounded-full border border-amber-500/30 bg-amber-500/10 px-2 py-0.5 text-xs font-semibold text-amber-300">Update available</span>
                                        @elseif ($module['database_version'])
                                            <span class="mt-1 inline-flex text-xs text-emerald-300">Database</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($module['enabled'])
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                                                <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Enabled
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-300">
                                                <i class="fa-solid fa-circle-pause" aria-hidden="true"></i> Disabled
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-zinc-400">
                                        {{ $module['updated_at']?->format('d M Y H:i') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('modules.control.show', $module['name']) }}" class="mc-view-button rounded-lg border px-3 py-1.5 text-sm font-semibold shadow-sm">View</a>
                                            @if ($module['enabled'])
                                                <button type="submit" formaction="{{ route('modules.control.disable', $module['name']) }}" class="rounded-lg border border-red-500/40 bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(! $module['can_disable'])>
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
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full border border-zinc-800 bg-zinc-950 text-zinc-500">
                                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                        </div>
                                        <div class="mt-3 font-semibold text-zinc-100">No modules found</div>
                                        <div class="mt-1 text-sm text-zinc-400">Adjust the search or filter and try again.</div>
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

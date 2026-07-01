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
        .mc-shell {
            --mc-card: rgb(24 24 27);
            --mc-card-soft: rgb(39 39 42 / .55);
            --mc-border: rgb(63 63 70);
            --mc-muted: rgb(161 161 170);
            --mc-white: rgb(244 244 245);
            --mc-emerald: rgb(52 211 153);
            --mc-blue: rgb(96 165 250);
            --mc-red: rgb(252 165 165);
        }
        .mc-hero {
            overflow: hidden;
            border-radius: 1.5rem;
            border: 1px solid rgb(63 63 70);
            background: linear-gradient(135deg, #020617 0%, #0f172a 45%, #172554 100%);
            color: #fff;
            box-shadow: 0 1px 2px rgb(0 0 0 / .35);
        }
        .mc-hero-inner {
            padding: 1.75rem 1.5rem;
        }
        @media (min-width: 1024px) {
            .mc-hero-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1.5rem;
            }
        }
        .mc-hero-eyebrow {
            font-size: .75rem;
            line-height: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .28em;
            color: rgb(191 219 254);
        }
        .mc-hero-title {
            margin-top: .5rem;
            font-size: 1.875rem;
            line-height: 2.25rem;
            font-weight: 650;
            letter-spacing: -.025em;
            color: white;
        }
        .mc-hero-copy {
            margin-top: .5rem;
            max-width: 48rem;
            font-size: .875rem;
            line-height: 1.55rem;
            color: rgb(219 234 254 / .9);
        }
        .mc-hero-panel {
            width: min(100%, 28rem);
            margin-top: 1rem;
            border-radius: 1rem;
            border: 1px solid rgb(255 255 255 / .16);
            background: rgb(15 23 42 / .52);
            padding: 1rem;
        }
        @media (min-width: 1024px) {
            .mc-hero-panel { margin-top: 0; }
        }
        .mc-hero-topline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .mc-hero-icon {
            display: inline-flex;
            height: 3rem;
            width: 3rem;
            align-items: center;
            justify-content: center;
            border-radius: .875rem;
            border: 1px solid rgb(52 211 153 / .65);
            background: rgb(52 211 153 / .12);
            color: rgb(167 243 208);
            box-shadow: inset 0 1px 0 rgb(255 255 255 / .08);
        }
        .mc-hero-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            border: 1px solid rgb(255 255 255 / .2);
            background: rgb(255 255 255 / .08);
            padding: .45rem .95rem;
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: rgb(241 245 249);
            white-space: nowrap;
        }
        .mc-hero-stats {
            margin-top: 1.25rem;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .mc-hero-stat {
            padding: 0 .85rem;
            border-left: 1px solid rgb(255 255 255 / .16);
        }
        .mc-hero-stat:first-child {
            border-left: 0;
            padding-left: 0;
        }
        .mc-hero-stat-value {
            font-size: 1.875rem;
            line-height: 2.25rem;
            font-weight: 900;
            color: white;
        }
        .mc-hero-stat-label {
            margin-top: .15rem;
            font-size: .7rem;
            font-weight: 900;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: rgb(226 232 240);
        }
        .mc-hero-action-row {
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
        }
        @media (min-width: 1024px) {
            .mc-hero-action-row { margin-top: 0; justify-content: flex-end; }
        }
        .mc-hero-action-icon {
            display: inline-flex;
            height: 3.25rem;
            width: 3.25rem;
            align-items: center;
            justify-content: center;
            border-radius: 1rem;
            border: 1px solid rgb(52 211 153 / .55);
            background: rgb(52 211 153 / .10);
            color: rgb(110 231 183);
        }
        .mc-hero-action-divider {
            height: 3.25rem;
            width: 1px;
            background: rgb(255 255 255 / .18);
        }
        .mc-back-button,
        .mc-back-button:visited {
            display: inline-flex;
            min-height: 3.25rem;
            align-items: center;
            justify-content: center;
            gap: .7rem;
            border-radius: .9rem;
            border: 1px solid rgb(52 211 153 / .55);
            background: rgb(16 185 129 / .08) !important;
            padding: .75rem 1.6rem;
            color: rgb(52 211 153) !important;
            font-size: .875rem;
            font-weight: 900;
            text-decoration: none !important;
            box-shadow: none !important;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease, transform .15s ease;
            white-space: nowrap;
        }
        .mc-back-button:hover,
        .mc-back-button:focus {
            background: rgb(16 185 129 / .16) !important;
            border-color: rgb(110 231 183 / .8) !important;
            color: rgb(167 243 208) !important;
            transform: translateY(-1px);
        }
        .mc-mini-status {
            width: min(100%, 28rem);
            border-radius: 1rem;
            border: 1px solid rgb(255 255 255 / .14);
            background: rgb(15 23 42 / .45);
            padding: 1rem;
        }
        .mc-mini-status-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .mc-mini-status-cell {
            padding: 0 1rem;
            border-left: 1px solid rgb(255 255 255 / .16);
        }
        .mc-mini-status-cell:first-child { border-left: 0; padding-left: 0; }
        .mc-row,
        .mc-row:hover,
        .mc-row:focus-within {
            background: rgb(24 24 27 / 0.68) !important;
            color: inherit !important;
        }
        .mc-row:hover {
            background: rgb(39 39 42 / 0.72) !important;
        }
        .mc-view-button,
        .mc-view-button:hover,
        .mc-view-button:focus,
        .mc-view-button:visited {
            background: rgb(22 163 74) !important;
            border-color: rgb(34 197 94 / 0.65) !important;
            color: #fff !important;
            text-decoration: none !important;
        }
        .mc-view-button:hover,
        .mc-view-button:focus {
            background: rgb(21 128 61) !important;
            color: #fff !important;
        }
        .mc-safe-button:hover,
        .mc-safe-button:focus {
            color: #fff !important;
        }
        .mc-shell table a:hover,
        .mc-shell table button:hover {
            color: #fff !important;
        }
    </style>

    <div class="mc-shell space-y-6">
        <section class="mc-hero">
            <div class="mc-hero-inner">
                <div>
                    <p class="mc-hero-eyebrow">Platform Administration</p>
                    <h1 class="mc-hero-title">Module Control</h1>
                    <p class="mc-hero-copy">
                        Manage installed modules, database metadata, dependencies and activation state.
                    </p>
                </div>

                <div class="mc-hero-panel" aria-label="Module status summary">
                    <div class="mc-hero-topline">
                        <span class="mc-hero-icon">
                            <i class="fa-solid fa-puzzle-piece text-xl" aria-hidden="true"></i>
                        </span>

                        <span class="mc-hero-pill">Database backed</span>
                    </div>

                    <div class="mc-hero-stats">
                        <div class="mc-hero-stat">
                            <div class="mc-hero-stat-value">{{ $installedCount }}</div>
                            <div class="mc-hero-stat-label">Installed</div>
                        </div>
                        <div class="mc-hero-stat">
                            <div class="mc-hero-stat-value" style="color: rgb(110 231 183);">{{ $enabledCount }}</div>
                            <div class="mc-hero-stat-label">Enabled</div>
                        </div>
                        <div class="mc-hero-stat">
                            <div class="mc-hero-stat-value" style="color: rgb(252 165 165);">{{ $disabledCount }}</div>
                            <div class="mc-hero-stat-label">Disabled</div>
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
                                                <button type="submit" formaction="{{ route('modules.control.disable', $module['name']) }}" class="mc-safe-button rounded-lg border border-red-500/40 bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(! $module['can_disable'])>
                                                    Disable
                                                </button>
                                            @else
                                                <button type="submit" formaction="{{ route('modules.control.enable', $module['name']) }}" class="mc-safe-button rounded-lg border border-emerald-500/40 bg-emerald-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-emerald-700">
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

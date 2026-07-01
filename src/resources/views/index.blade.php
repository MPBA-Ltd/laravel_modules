<x-layouts.app :title="__('Module Control')">
    <x-dynamic-component
        :component="config('modules.admin.hero_component', 'wms.page-hero')"
        title="Module Control"
        subtitle="Control installed modules from the database-backed module registry."
    />

    @php
        $enabledCount = $allModules->where('enabled', true)->count();
        $disabledCount = $allModules->count() - $enabledCount;
    @endphp

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Installed</div>
                <div class="mt-2 text-3xl font-bold text-zinc-900 dark:text-zinc-100">{{ $allModules->count() }}</div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Enabled</div>
                <div class="mt-2 text-3xl font-bold text-emerald-700 dark:text-emerald-300">{{ $enabledCount }}</div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">Disabled</div>
                <div class="mt-2 text-3xl font-bold text-red-700 dark:text-red-300">{{ $disabledCount }}</div>
            </div>
        </div>

        <div class="flex flex-col gap-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Installed modules</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Enabled state, sort order, custom descriptions, and version numbers are stored only in the database.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <form method="GET" action="{{ route('modules.control.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label for="module-search" class="sr-only">Search modules</label>
                    <input
                        id="module-search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search modules..."
                        class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 placeholder:text-zinc-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100 dark:placeholder:text-zinc-500 sm:w-72"
                    />
                    <button type="submit" class="rounded-xl border border-blue-500/40 bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                        Search
                    </button>
                    @if ($search !== '')
                        <a href="{{ route('modules.control.index') }}" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                            Clear
                        </a>
                    @endif
                </form>

                <form method="POST" action="{{ route('modules.control.sync') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                        Sync Database
                    </button>
                </form>
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

        <div class="grid gap-4 xl:grid-cols-2">
            @forelse ($modules as $module)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:border-zinc-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-zinc-950 dark:text-zinc-100">
                                    {{ $module['name'] }}
                                </h3>

                                @if ($module['enabled'])
                                    <span class="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                        Enabled
                                    </span>
                                @else
                                    <span class="rounded-full border border-red-500/30 bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-300">
                                        Disabled
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-800 dark:bg-zinc-950/60">
                                <div class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                    Database description
                                </div>
                                <p class="mt-1 line-clamp-3 text-sm leading-6 text-zinc-700 dark:text-zinc-300">
                                    {{ $module['description'] ?: 'No database description saved yet.' }}
                                </p>
                            </div>

                            @if ($module['disk_description'])
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-zinc-500 dark:text-zinc-500">
                                    Disk description: {{ $module['disk_description'] }}
                                </p>
                            @endif
                        </div>

                        <div class="shrink-0 text-right text-xs text-zinc-500">
                            <div>DB ID: {{ $module['status_record']?->id ?? '—' }}</div>
                            <div>Order: {{ $module['sort_order'] }}</div>
                        </div>
                    </div>

                    <dl class="mt-4 grid gap-3 border-t border-zinc-100 pt-4 text-sm dark:border-zinc-800 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs font-semibold uppercase text-zinc-500">Alias</dt>
                            <dd class="mt-1 text-zinc-800 dark:text-zinc-200">{{ $module['alias'] }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase text-zinc-500">Version</dt>
                            <dd class="mt-1 text-zinc-800 dark:text-zinc-200">
                                {{ $module['version'] ?: '—' }}
                                @if ($module['database_version'])
                                    <span class="ml-1 text-xs text-blue-600 dark:text-blue-300">DB</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase text-zinc-500">Requires</dt>
                            <dd class="mt-1 text-zinc-800 dark:text-zinc-200">{{ empty($module['requires']) ? 'None' : count($module['requires']) }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 truncate text-xs text-zinc-400">
                        {{ $module['path'] }}
                    </div>

                    <div class="mt-5 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('modules.control.show', $module['name']) }}"
                           class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                            View / Edit
                        </a>

                        @if ($module['enabled'])
                            <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                                @csrf
                                <button type="submit" class="rounded-xl border border-red-500/40 bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                                    Disable
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                                @csrf
                                <button type="submit" class="rounded-xl border border-emerald-500/40 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                    Enable
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">
                    No modules found on disk.
                </div>
            @endforelse
        </div>

        @if ($modules->hasPages())
            <div class="rounded-2xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                {{ $modules->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>

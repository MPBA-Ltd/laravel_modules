<x-layouts.app :title="$module['name'].' Module'">
    <x-dynamic-component
        :component="config('modules.admin.hero_component', 'wms.page-hero')"
        :title="$module['name'].' Module'"
        subtitle="Database-backed module details and activation state."
    />

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-900/60 dark:bg-green-950/40 dark:text-green-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 xl:col-span-2">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Database metadata</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    This description is stored in the module_statuses table only. It does not edit module.json.
                </p>

                <form method="POST" action="{{ route('modules.control.update', $module['name']) }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="description" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Description
                        </label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="Add the database-only module description..."
                        >{{ old('description', $module['description']) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="max-w-xs">
                        <label for="sort_order" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                            Sort order
                        </label>
                        <input
                            id="sort_order"
                            name="sort_order"
                            type="number"
                            min="0"
                            value="{{ old('sort_order', $module['sort_order']) }}"
                            class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        />
                        @error('sort_order')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save Database Metadata
                        </button>

                        <a href="{{ route('modules.control.index') }}" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                            Back
                        </a>
                    </div>
                </form>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Activation</h2>

                <div class="mt-4">
                    @if ($module['enabled'])
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-200">Enabled</span>
                    @else
                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-200">Disabled</span>
                    @endif
                </div>

                <div class="mt-6">
                    @if ($module['enabled'])
                        <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Disable Module
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                Enable Module
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Module details</h2>

            <dl class="mt-6 grid gap-5 md:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Module</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['name'] }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Alias</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['alias'] }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Version</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['version'] ?: '—' }}</dd>
                </div>

                <div>
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Database record</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">ID {{ $module['status_record']?->id ?? '—' }}</dd>
                </div>

                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Path</dt>
                    <dd class="mt-1 break-all text-sm text-zinc-900 dark:text-zinc-100">{{ $module['path'] }}</dd>
                </div>

                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Requires</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ empty($module['requires']) ? 'None' : implode(', ', $module['requires']) }}
                    </dd>
                </div>

                @if ($module['disk_description'])
                    <div class="md:col-span-2">
                        <dt class="text-xs font-semibold uppercase text-zinc-500">Disk description</dt>
                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['disk_description'] }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</x-layouts.app>

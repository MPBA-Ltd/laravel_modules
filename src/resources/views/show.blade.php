@include('modules-control::partials.layout-open', ['title' => $module['name'].' Module'])
    <x-dynamic-component
        :component="config('modules.admin.hero_component', 'wms.page-hero')"
        :title="$module['name'].' Module'"
        subtitle="Database-backed module details and activation state."
    />

    <div class="space-y-6">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <dl class="grid gap-4 md:grid-cols-2">
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
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Database enabled</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['enabled'] ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Path</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $module['path'] }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-xs font-semibold uppercase text-zinc-500">Requires</dt>
                    <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                        {{ empty($module['requires']) ? 'None' : implode(', ', $module['requires']) }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('modules.control.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">Back</a>

            @if ($module['enabled'])
                <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Disable Module</button>
                </form>
            @else
                <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Enable Module</button>
                </form>
            @endif
        </div>
    </div>
@include('modules-control::partials.layout-close')

<x-layouts.app :title="__('Module Control')">
    <x-dynamic-component
        :component="config('modules.admin.hero_component', 'wms.page-hero')"
        title="Module Control"
        subtitle="Database-backed module status control using the module_statuses table."
    />

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Installed modules</h2>
                <p class="text-sm text-zinc-500">Status is read from and written to the database, not modules_statuses.json.</p>
            </div>

            <form method="POST" action="{{ route('modules.control.sync') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                    Sync Database
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <thead class="bg-zinc-50 dark:bg-zinc-950">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-zinc-500">Module</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-zinc-500">Database Record</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-zinc-500">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-zinc-500">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($modules as $module)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $module['name'] }}</div>
                                <div class="text-sm text-zinc-500">{{ $module['description'] ?: 'No description provided.' }}</div>
                                <div class="mt-1 text-xs text-zinc-400">{{ $module['path'] }}</div>
                            </td>

                            <td class="px-4 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                @if ($module['status_record'])
                                    <div>ID: {{ $module['status_record']->id }}</div>
                                    <div>module: {{ $module['status_record']->module }}</div>
                                @else
                                    <span class="text-red-600">Missing</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                @if ($module['enabled'])
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Enabled</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-700">Disabled</span>
                                @endif
                            </td>

                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('modules.control.show', $module['name']) }}" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-sm hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">View</a>

                                    @if ($module['enabled'])
                                        <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700">Disable</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-700">Enable</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-zinc-500">No modules found on disk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>

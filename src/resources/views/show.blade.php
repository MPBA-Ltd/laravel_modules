<x-layouts.app :title="$module['name'].' Module'">
    @php
        $fileGroups = [
            'Routes' => $module['routes'] ?? [],
            'Migrations' => $module['migrations'] ?? [],
            'Models' => $module['models'] ?? [],
            'Controllers' => $module['controllers'] ?? [],
            'Providers' => $module['providers'] ?? [],
        ];
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-blue-900/40 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-950 p-8 text-white shadow-sm sm:p-10">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_38rem] lg:items-start">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.34em] text-cyan-300">{{ __('Module Control') }}</p>
                    <h1 class="mt-6 text-4xl font-black tracking-tight text-white sm:text-5xl">{{ $module['name'] }} {{ __('Module') }}</h1>
                    <p class="mt-5 max-w-2xl text-base font-semibold leading-7 text-blue-50/90 sm:text-lg">
                        {{ __('Database metadata, dependencies, developer information and activation controls.') }}
                    </p>
                </div>

                <div class="space-y-6 lg:mt-1">
                    <div class="flex items-center justify-end gap-6">
                        <span class="inline-flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-emerald-300/60 bg-emerald-400/10 text-emerald-200 shadow-sm">
                            <i class="fa-solid {{ $module['icon'] ?: 'fa-chart-line' }} text-2xl" aria-hidden="true"></i>
                        </span>

                        <div class="h-16 w-px bg-white/15"></div>

                        <a href="{{ route('modules.control.index') }}" wire:navigate class="inline-flex min-h-14 items-center justify-center gap-3 rounded-2xl border border-emerald-400/60 bg-emerald-600 px-7 py-3 text-sm font-black text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-300/60">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            {{ __('Back to Module Control') }}
                        </a>
                    </div>

                    <div class="rounded-2xl border border-white/25 bg-slate-950/25 p-5 shadow-sm ring-1 ring-white/10 backdrop-blur">
                        <div class="grid grid-cols-2 divide-x divide-white/15">
                            <div class="pr-5">
                                <div class="text-xs font-black uppercase tracking-[0.24em] text-blue-100/90">{{ __('Status') }}</div>
                                <div class="mt-4 text-2xl font-black {{ $module['enabled'] ? 'text-emerald-300' : 'text-red-300' }}">
                                    {{ $module['enabled'] ? __('Enabled') : __('Disabled') }}
                                </div>
                            </div>
                            <div class="pl-5">
                                <div class="text-xs font-black uppercase tracking-[0.24em] text-blue-100/90">{{ __('Requires') }}</div>
                                <div class="mt-4 text-2xl font-black text-white">{{ count($module['requires'] ?? []) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @foreach (['status' => 'emerald', 'warning' => 'amber', 'error' => 'red'] as $flash => $tone)
            @if (session($flash))
                <div class="rounded-2xl border px-4 py-3 text-sm font-medium {{ $tone === 'emerald' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : ($tone === 'amber' ? 'border-amber-500/40 bg-amber-500/10 text-amber-700 dark:text-amber-300' : 'border-red-500/40 bg-red-500/10 text-red-700 dark:text-red-300') }}">
                    {{ session($flash) }}
                </div>
            @endif
        @endforeach

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_24rem]">
            <div class="space-y-6">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex items-start gap-4">
                            <span class="inline-flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-zinc-200 bg-zinc-950 text-white shadow-sm dark:border-zinc-700">
                                <i class="fa-solid {{ $module['icon'] ?: 'fa-puzzle-piece' }} text-2xl" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-2xl font-black text-zinc-950 dark:text-zinc-100">{{ $module['name'] }}</h2>
                                    @if ($module['enabled'])
                                        <span class="rounded-full border border-emerald-500/40 bg-emerald-500/10 px-3 py-1 text-xs font-black text-emerald-700 dark:text-emerald-300">{{ __('Enabled') }}</span>
                                    @else
                                        <span class="rounded-full border border-red-500/40 bg-red-500/10 px-3 py-1 text-xs font-black text-red-700 dark:text-red-300">{{ __('Disabled') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $module['alias'] }}</p>
                                <p class="mt-4 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $module['description'] ?: 'No database description saved yet.' }}</p>
                            </div>
                        </div>

                        <div class="grid min-w-64 grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/70">
                                <div class="text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Version') }}</div>
                                <div class="mt-2 font-bold text-zinc-950 dark:text-zinc-100">{{ $module['version'] ?: '—' }}</div>
                            </div>
                            <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/70">
                                <div class="text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Author') }}</div>
                                <div class="mt-2 font-bold text-zinc-950 dark:text-zinc-100">{{ $module['author'] ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ __('Database metadata') }}</h2>
                    <form method="POST" action="{{ route('modules.control.update', $module['name']) }}" class="mt-5 grid gap-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Description') }}</label>
                            <textarea name="description" rows="4" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">{{ old('description', $module['database_description']) }}</textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Version') }}</label>
                                <input name="version" value="{{ old('version', $module['database_version']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Sort order') }}</label>
                                <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $module['sort_order']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Icon') }}</label>
                                <input name="icon" value="{{ old('icon', $module['icon']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Category') }}</label>
                                <input name="category" value="{{ old('category', $module['category']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Vendor type') }}</label>
                                <input name="vendor_type" value="{{ old('vendor_type', $module['vendor_type']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Author') }}</label>
                                <input name="author" value="{{ old('author', $module['author']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wide text-zinc-500">{{ __('Notes') }}</label>
                            <textarea name="notes" rows="3" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">{{ old('notes', $module['notes']) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl border border-blue-500/40 bg-blue-600 px-5 py-2 text-sm font-black text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400/50">
                                {{ __('Save metadata') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ __('Dependencies') }}</h2>
                        <div class="mt-5 grid gap-4">
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">{{ __('Requires') }}</div>
                                <div class="mt-1 text-zinc-800 dark:text-zinc-200">{{ empty($module['requires']) ? 'None' : implode(', ', $module['requires']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">{{ __('Missing dependencies') }}</div>
                                <div class="mt-1 {{ $module['has_missing_dependencies'] ? 'text-orange-700 dark:text-orange-300' : 'text-zinc-800 dark:text-zinc-200' }}">{{ empty($module['missing_dependencies']) ? 'None' : implode(', ', $module['missing_dependencies']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">{{ __('Enabled dependents') }}</div>
                                <div class="mt-1 {{ $module['can_disable'] ? 'text-zinc-800 dark:text-zinc-200' : 'text-orange-700 dark:text-orange-300' }}">{{ empty($module['enabled_dependents']) ? 'None' : implode(', ', $module['enabled_dependents']) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ __('Activation') }}</h2>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Disabling is blocked when another enabled module depends on this module.') }}</p>
                        <div class="mt-5">
                            @if ($module['enabled'])
                                <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-red-500/40 bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(! $module['can_disable'])>{{ __('Disable Module') }}</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-emerald-500/40 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">{{ __('Enable Module') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <details class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-4 py-3 text-white marker:hidden sm:px-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-code text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-blue-200">{{ __('Developer') }}</p>
                                <h2 class="truncate text-base font-extrabold tracking-tight">{{ __('Developer files') }}</h2>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            {{ __('Files') }}
                            <i class="fa-solid fa-chevron-right transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <div class="p-5">
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($fileGroups as $label => $files)
                                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-800 dark:bg-zinc-950/60">
                                    <div class="flex items-center justify-between gap-3">
                                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $label }}</h3>
                                        <span class="rounded-full border border-zinc-300 px-2 py-0.5 text-xs font-semibold text-zinc-600 dark:border-zinc-700 dark:text-zinc-300">{{ count($files) }}</span>
                                    </div>
                                    <ul class="mt-3 max-h-36 space-y-1 overflow-auto text-xs text-zinc-600 dark:text-zinc-400">
                                        @forelse ($files as $file)
                                            <li class="break-all">{{ $file }}</li>
                                        @empty
                                            <li>{{ __('None found.') }}</li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>

                <div class="grid gap-6 lg:grid-cols-2">
                    <details class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-4 py-3 text-white marker:hidden sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                    <i class="fa-solid fa-book-open text-sm" aria-hidden="true"></i>
                                </span>
                                <h2 class="truncate text-base font-extrabold tracking-tight">{{ __('README preview') }}</h2>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                                <i class="fa-solid fa-chevron-right transition group-open:rotate-90" aria-hidden="true"></i>
                            </span>
                        </summary>
                        <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-5 text-xs leading-6 text-zinc-700 dark:text-zinc-300">{{ $module['readme'] ?: 'No README found.' }}</pre>
                    </details>

                    <details open class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-4 py-3 text-white marker:hidden sm:px-5">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                    <i class="fa-solid fa-clock-rotate-left text-sm" aria-hidden="true"></i>
                                </span>
                                <h2 class="truncate text-base font-extrabold tracking-tight">{{ __('Changelog preview') }}</h2>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                                <i class="fa-solid fa-chevron-right transition group-open:rotate-90" aria-hidden="true"></i>
                            </span>
                        </summary>
                        <pre class="max-h-96 overflow-auto whitespace-pre-wrap p-5 text-xs leading-6 text-zinc-700 dark:text-zinc-300">{{ $module['changelog'] ?: 'No changelog found.' }}</pre>
                    </details>
                </div>
            </div>

            <aside class="space-y-6">
                <details class="group overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-4 py-3 text-white marker:hidden sm:px-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-terminal text-sm" aria-hidden="true"></i>
                            </span>
                            <h2 class="truncate text-base font-extrabold tracking-tight">{{ __('Developer information') }}</h2>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            <i class="fa-solid fa-chevron-right transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <dl class="space-y-4 p-5 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Installed path') }}</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['path'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Namespace') }}</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['namespace'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Composer package') }}</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['composer_name'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Git commit') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['git_commit'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Build date') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['build_date'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Database record') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">ID {{ $module['status_record']?->id ?? '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Last updated') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['updated_at']?->format('d M Y H:i') ?? '—' }}</dd></div>
                    </dl>
                </details>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">{{ __('Disk metadata') }}</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Alias') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['alias'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Disk version') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['disk_version'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">{{ __('Disk description') }}</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['disk_description'] ?: '—' }}</dd></div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>

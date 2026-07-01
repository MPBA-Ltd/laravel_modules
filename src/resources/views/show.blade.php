<x-layouts.app :title="$module['name'].' Module'">
    <section class="overflow-hidden rounded-2xl border border-slate-700/60 bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 px-8 py-8 shadow-2xl shadow-slate-950/30 sm:px-10">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(26rem,35rem)] lg:items-start">
            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-[0.30em] text-cyan-300">
                    {{ __('Module Control') }}
                </p>

                <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-5xl">
                    {{ $module['name'] }} {{ __('Module') }}
                </h1>

                <p class="mt-5 max-w-3xl text-base font-semibold leading-7 text-blue-50/85 sm:text-lg">
                    {{ __('Database metadata, dependencies, developer information and activation controls.') }}
                </p>
            </div>

            <div class="space-y-5">
                <div class="flex items-center justify-end gap-5">
                    <span class="inline-flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-emerald-300/70 bg-emerald-400/10 text-emerald-200 shadow-sm">
                        <i class="fa-solid {{ $module['icon'] ?: 'fa-chart-line' }} text-2xl" aria-hidden="true"></i>
                    </span>

                    <div class="h-16 w-px bg-white/15"></div>

                    <a href="{{ route('modules.control.index') }}" wire:navigate
                       class="inline-flex min-h-14 items-center justify-center gap-3 rounded-2xl border border-emerald-400/50 bg-emerald-600/15 px-6 py-3 text-sm font-black text-emerald-100 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-600/30 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-300/50">
                        <i class="fa-solid fa-arrow-left text-sm" aria-hidden="true"></i>
                        {{ __('Back to Module Control') }}
                    </a>
                </div>

                <div class="rounded-2xl border border-white/15 bg-slate-950/25 p-5 shadow-xl backdrop-blur">
                    <div class="grid grid-cols-2 divide-x divide-white/15">
                        <div class="px-4 first:pl-0">
                            <div class="text-xs font-black uppercase tracking-[0.20em] text-blue-100/80">{{ __('Status') }}</div>
                            <div class="mt-3 text-2xl font-black {{ $module['enabled'] ? 'text-emerald-300' : 'text-red-300' }}">
                                {{ $module['enabled'] ? __('Enabled') : __('Disabled') }}
                            </div>
                        </div>

                        <div class="px-4">
                            <div class="text-xs font-black uppercase tracking-[0.20em] text-blue-100/80">{{ __('Requires') }}</div>
                            <div class="mt-3 text-2xl font-black text-white">{{ count($module['requires']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $fileGroups = [
            'Routes' => $module['routes'] ?? [],
            'Migrations' => $module['migrations'] ?? [],
            'Models' => $module['models'] ?? [],
            'Controllers' => $module['controllers'] ?? [],
            'Service Providers' => $module['providers'] ?? [],
        ];
    @endphp

    <div class="space-y-6">
        @foreach (['status' => 'emerald', 'warning' => 'amber', 'error' => 'red'] as $flash => $tone)
            @if (session($flash))
                <div class="rounded-2xl border px-4 py-3 text-sm font-medium {{ $tone === 'emerald' ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-700 dark:text-emerald-300' : ($tone === 'amber' ? 'border-amber-500/40 bg-amber-500/10 text-amber-700 dark:text-amber-300' : 'border-red-500/40 bg-red-500/10 text-red-700 dark:text-red-300') }}">
                    {{ session($flash) }}
                </div>
            @endif
        @endforeach

        <div class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200">
                        <i class="fa-solid {{ $module['icon'] ?: 'fa-puzzle-piece' }}" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase text-zinc-500">Module</div>
                        <div class="font-semibold text-zinc-950 dark:text-zinc-100">{{ $module['name'] }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase text-zinc-500">Status</div>
                <div class="mt-2">
                    @if ($module['enabled'])
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300"><i class="fa-solid fa-circle-check"></i> Enabled</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-300"><i class="fa-solid fa-circle-pause"></i> Disabled</span>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase text-zinc-500">Version</div>
                <div class="mt-2 font-semibold text-zinc-950 dark:text-zinc-100">{{ $module['version'] ?: '—' }}</div>
                @if ($module['has_update'])
                    <div class="mt-1 text-xs font-semibold text-amber-700 dark:text-amber-300">Update available</div>
                @endif
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="text-xs font-semibold uppercase text-zinc-500">Dependencies</div>
                <div class="mt-2 font-semibold {{ $module['has_missing_dependencies'] ? 'text-orange-700 dark:text-orange-300' : 'text-zinc-950 dark:text-zinc-100' }}">
                    {{ count($module['requires']) }} required
                </div>
                @if ($module['has_missing_dependencies'])
                    <div class="mt-1 text-xs text-orange-700 dark:text-orange-300">Missing dependency</div>
                @endif
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Database metadata</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">These fields are saved only in the module_statuses table.</p>
                    </div>

                    <form method="POST" action="{{ route('modules.control.update', $module['name']) }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="description" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Description</label>
                            <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" placeholder="Add the database-only module description...">{{ old('description', $module['database_description'] ?? $module['description']) }}</textarea>
                            @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="version" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Version</label>
                                <input id="version" name="version" type="text" maxlength="50" value="{{ old('version', $module['database_version'] ?? $module['version']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" placeholder="Example: 1.0.0" />
                                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">Disk version: {{ $module['disk_version'] ?: '—' }}</p>
                                @error('version')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="sort_order" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Sort order</label>
                                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $module['sort_order']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                                @error('sort_order')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Font Awesome icon</label>
                                <input id="icon" name="icon" type="text" maxlength="80" value="{{ old('icon', $module['icon']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" placeholder="fa-puzzle-piece" />
                                @error('icon')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Category</label>
                                <select id="category" name="category" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                                    @foreach (['Core', 'Optional'] as $option)
                                        <option value="{{ $option }}" @selected(old('category', $module['category']) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="vendor_type" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Vendor type</label>
                                <select id="vendor_type" name="vendor_type" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                                    @foreach (['MPBA', 'Third-party'] as $option)
                                        <option value="{{ $option }}" @selected(old('vendor_type', $module['vendor_type']) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="author" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Author</label>
                                <input id="author" name="author" type="text" maxlength="120" value="{{ old('author', $module['author']) }}" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-2 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100" />
                                @error('author')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-zinc-800 dark:text-zinc-200">Internal notes</label>
                            <textarea id="notes" name="notes" rows="4" class="mt-2 w-full rounded-xl border border-zinc-300 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">{{ old('notes', $module['notes']) }}</textarea>
                            @error('notes')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <button type="submit" class="rounded-xl border border-blue-500/40 bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/30">Save Module Metadata</button>
                    </form>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Dependencies</h2>
                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Required modules</div>
                                <div class="mt-1 text-zinc-800 dark:text-zinc-200">{{ empty($module['requires']) ? 'None' : implode(', ', $module['requires']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Missing dependencies</div>
                                <div class="mt-1 {{ $module['has_missing_dependencies'] ? 'text-orange-700 dark:text-orange-300' : 'text-zinc-800 dark:text-zinc-200' }}">{{ empty($module['missing_dependencies']) ? 'None' : implode(', ', $module['missing_dependencies']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Enabled dependents</div>
                                <div class="mt-1 {{ $module['can_disable'] ? 'text-zinc-800 dark:text-zinc-200' : 'text-orange-700 dark:text-orange-300' }}">{{ empty($module['enabled_dependents']) ? 'None' : implode(', ', $module['enabled_dependents']) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Activation</h2>
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Disabling is blocked when another enabled module depends on this module.</p>
                        <div class="mt-5">
                            @if ($module['enabled'])
                                <form method="POST" action="{{ route('modules.control.disable', $module['name']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-red-500/40 bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50" @disabled(! $module['can_disable'])>Disable Module</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('modules.control.enable', $module['name']) }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-xl border border-emerald-500/40 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Enable Module</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Developer files</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
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
                                        <li>None found.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">README preview</h2>
                        <pre class="mt-4 max-h-96 overflow-auto whitespace-pre-wrap rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-xs leading-6 text-zinc-700 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300">{{ $module['readme'] ?: 'No README found.' }}</pre>
                    </div>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Changelog preview</h2>
                        <pre class="mt-4 max-h-96 overflow-auto whitespace-pre-wrap rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-xs leading-6 text-zinc-700 dark:border-zinc-800 dark:bg-zinc-950 dark:text-zinc-300">{{ $module['changelog'] ?: 'No changelog found.' }}</pre>
                    </div>
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Developer information</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Installed path</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['path'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Namespace</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['namespace'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Composer package</dt><dd class="mt-1 break-all text-zinc-900 dark:text-zinc-100">{{ $module['composer_name'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Git commit</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['git_commit'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Build date</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['build_date'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Database record</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">ID {{ $module['status_record']?->id ?? '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Last updated</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['updated_at']?->format('d M Y H:i') ?? '—' }}</dd></div>
                    </dl>
                </div>

                <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold text-zinc-950 dark:text-zinc-100">Disk metadata</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Alias</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['alias'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Disk version</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['disk_version'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Disk description</dt><dd class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $module['disk_description'] ?: '—' }}</dd></div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>

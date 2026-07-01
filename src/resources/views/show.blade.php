<x-layouts.app :title="$module['name'].' Module'">
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
        <section class="overflow-hidden rounded-[1.35rem] border border-blue-400/20 bg-[radial-gradient(circle_at_25%_12%,rgba(22,163,184,0.18),transparent_30%),linear-gradient(135deg,#050b21_0%,#0b1a47_52%,#07111f_100%)] px-8 py-9 shadow-2xl shadow-black/30 sm:px-10 lg:px-12">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(29rem,36rem)] lg:items-start">
                <div class="max-w-3xl">
                    <p class="text-xs font-black uppercase tracking-[0.35em] text-emerald-300">Module Control</p>
                    <h1 class="mt-6 text-4xl font-black tracking-tight text-white sm:text-5xl">{{ $module['name'] }} Module</h1>
                    <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-slate-200">
                        Database metadata, dependencies, developer information and activation controls.
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-center justify-end gap-6">
                        <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-emerald-300/55 bg-emerald-400/10 text-emerald-200 shadow-sm">
                            <i class="fa-solid fa-chart-line text-3xl" aria-hidden="true"></i>
                        </span>

                        <div class="h-16 w-px bg-white/20"></div>

                        <a href="{{ route('modules.control.index') }}" wire:navigate
                           class="inline-flex min-h-14 items-center justify-center gap-3 rounded-2xl border border-emerald-400/55 bg-emerald-500/5 px-8 py-3 text-sm font-black text-emerald-300 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-500/15 hover:text-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            {{ __('Back to Module Control') }}
                        </a>
                    </div>

                    <div class="rounded-[1.35rem] border border-white/15 bg-slate-950/25 p-6 shadow-2xl shadow-black/20 ring-1 ring-white/10 backdrop-blur">
                        <div class="grid grid-cols-2 gap-0">
                            <div class="border-r border-white/20 pr-8">
                                <div class="text-xs font-black uppercase tracking-[0.22em] text-slate-300">Status</div>
                                <div class="mt-4 text-2xl font-black {{ $module['enabled'] ? 'text-emerald-300' : 'text-red-300' }}">
                                    {{ $module['enabled'] ? 'Enabled' : 'Disabled' }}
                                </div>
                            </div>
                            <div class="pl-8">
                                <div class="text-xs font-black uppercase tracking-[0.22em] text-slate-300">Requires</div>
                                <div class="mt-4 text-2xl font-black text-white">{{ count($module['requires']) }}</div>
                            </div>
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

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl border border-zinc-700 bg-zinc-950 text-zinc-100">
                            <i class="fa-solid {{ $module['icon'] ?: 'fa-puzzle-piece' }} text-xl" aria-hidden="true"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-semibold uppercase text-zinc-500">Module</div>
                            <h2 class="mt-1 text-2xl font-black text-zinc-100">{{ $module['name'] }}</h2>
                            <p class="mt-1 text-sm text-zinc-400">{{ $module['alias'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-sm">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-100">Database metadata</h2>
                        <p class="mt-1 text-sm text-zinc-400">These fields are saved only in the module_statuses table.</p>
                    </div>

                    <form method="POST" action="{{ route('modules.control.update', $module['name']) }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="description" class="block text-sm font-semibold text-zinc-200">Description</label>
                            <textarea id="description" name="description" rows="5" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" placeholder="Add the database-only module description...">{{ old('description', $module['database_description'] ?? $module['description']) }}</textarea>
                            @error('description')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="version" class="block text-sm font-semibold text-zinc-200">Version</label>
                                <input id="version" name="version" type="text" maxlength="50" value="{{ old('version', $module['database_version'] ?? $module['version']) }}" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" placeholder="Example: 1.0.0" />
                                <p class="mt-2 text-xs text-zinc-400">Disk version: {{ $module['disk_version'] ?: '—' }}</p>
                                @error('version')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="sort_order" class="block text-sm font-semibold text-zinc-200">Sort order</label>
                                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $module['sort_order']) }}" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                @error('sort_order')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-semibold text-zinc-200">Font Awesome icon</label>
                                <input id="icon" name="icon" type="text" maxlength="80" value="{{ old('icon', $module['icon']) }}" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" placeholder="fa-puzzle-piece" />
                                @error('icon')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-semibold text-zinc-200">Category</label>
                                <select id="category" name="category" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach (['Core', 'Optional'] as $option)
                                        <option value="{{ $option }}" @selected(old('category', $module['category']) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="vendor_type" class="block text-sm font-semibold text-zinc-200">Vendor type</label>
                                <select id="vendor_type" name="vendor_type" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                    @foreach (['MPBA', 'Third-party'] as $option)
                                        <option value="{{ $option }}" @selected(old('vendor_type', $module['vendor_type']) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                                @error('vendor_type')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="author" class="block text-sm font-semibold text-zinc-200">Author</label>
                                <input id="author" name="author" type="text" maxlength="120" value="{{ old('author', $module['author']) }}" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" />
                                @error('author')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-semibold text-zinc-200">Notes</label>
                            <textarea id="notes" name="notes" rows="4" class="mt-2 w-full rounded-xl border border-zinc-700 bg-zinc-950 px-4 py-3 text-sm text-zinc-100 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20" placeholder="Internal notes...">{{ old('notes', $module['notes']) }}</textarea>
                            @error('notes')<p class="mt-2 text-sm text-red-400">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl border border-emerald-500/40 bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                Save Metadata
                            </button>
                        </div>
                    </form>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-zinc-100">Dependency safety</h2>
                        <div class="mt-5 space-y-4 text-sm">
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Requires</div>
                                <div class="mt-1 text-zinc-200">{{ empty($module['requires']) ? 'None' : implode(', ', $module['requires']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Missing dependencies</div>
                                <div class="mt-1 {{ $module['has_missing_dependencies'] ? 'text-orange-300' : 'text-zinc-200' }}">{{ empty($module['missing_dependencies']) ? 'None' : implode(', ', $module['missing_dependencies']) }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-semibold uppercase text-zinc-500">Enabled dependents</div>
                                <div class="mt-1 {{ $module['can_disable'] ? 'text-zinc-200' : 'text-orange-300' }}">{{ empty($module['enabled_dependents']) ? 'None' : implode(', ', $module['enabled_dependents']) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-zinc-100">Activation</h2>
                        <p class="mt-2 text-sm text-zinc-400">Disabling is blocked when another enabled module depends on this module.</p>
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

                <details class="group overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-5 py-4 text-white marker:hidden">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-code text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-blue-200">Developer</p>
                                <h2 class="truncate text-base font-extrabold tracking-tight">Developer files</h2>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            {{ collect($fileGroups)->flatten()->count() }} files
                            <i class="fa-solid fa-chevron-right text-xs transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($fileGroups as $label => $files)
                            <div class="rounded-xl border border-zinc-800 bg-zinc-950/60 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-sm font-semibold text-zinc-100">{{ $label }}</h3>
                                    <span class="rounded-full border border-zinc-700 px-2 py-0.5 text-xs font-semibold text-zinc-300">{{ count($files) }}</span>
                                </div>
                                <ul class="mt-3 max-h-36 space-y-1 overflow-auto text-xs text-zinc-400">
                                    @forelse ($files as $file)
                                        <li class="break-all">{{ $file }}</li>
                                    @empty
                                        <li>None found.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </details>

                <details class="group overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-5 py-4 text-white marker:hidden">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-book-open text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-blue-200">Documentation</p>
                                <h2 class="truncate text-base font-extrabold tracking-tight">README preview</h2>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            Preview
                            <i class="fa-solid fa-chevron-right text-xs transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <div class="p-5">
                        <pre class="max-h-96 overflow-auto whitespace-pre-wrap rounded-xl border border-zinc-800 bg-zinc-950 p-4 text-xs leading-6 text-zinc-300">{{ $module['readme'] ?: 'No README found.' }}</pre>
                    </div>
                </details>

                <details class="group overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-5 py-4 text-white marker:hidden">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-list-check text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-blue-200">Release history</p>
                                <h2 class="truncate text-base font-extrabold tracking-tight">Changelog preview</h2>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            Preview
                            <i class="fa-solid fa-chevron-right text-xs transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <div class="p-5">
                        <pre class="max-h-96 overflow-auto whitespace-pre-wrap rounded-xl border border-zinc-800 bg-zinc-950 p-4 text-xs leading-6 text-zinc-300">{{ $module['changelog'] ?: 'No changelog found.' }}</pre>
                    </div>
                </details>
            </div>

            <aside class="space-y-6">
                <details class="group overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900 shadow-sm">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 bg-gradient-to-r from-slate-950 via-blue-950 to-slate-900 px-5 py-4 text-white marker:hidden">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/25 text-white ring-1 ring-white/20">
                                <i class="fa-solid fa-circle-info text-sm" aria-hidden="true"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-[0.65rem] font-black uppercase tracking-[0.18em] text-blue-200">Developer</p>
                                <h2 class="truncate text-base font-extrabold tracking-tight">Developer information</h2>
                            </div>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-white/15 px-3 py-1.5 text-xs font-extrabold ring-1 ring-white/20">
                            Details
                            <i class="fa-solid fa-chevron-right text-xs transition group-open:rotate-90" aria-hidden="true"></i>
                        </span>
                    </summary>
                    <dl class="space-y-4 p-5 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Installed path</dt><dd class="mt-1 break-all text-zinc-100">{{ $module['path'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Namespace</dt><dd class="mt-1 break-all text-zinc-100">{{ $module['namespace'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Composer package</dt><dd class="mt-1 break-all text-zinc-100">{{ $module['composer_name'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Git commit</dt><dd class="mt-1 text-zinc-100">{{ $module['git_commit'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Build date</dt><dd class="mt-1 text-zinc-100">{{ $module['build_date'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Database record</dt><dd class="mt-1 text-zinc-100">ID {{ $module['status_record']?->id ?? '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Last updated</dt><dd class="mt-1 text-zinc-100">{{ $module['updated_at']?->format('d M Y H:i') ?? '—' }}</dd></div>
                    </dl>
                </details>

                <div class="rounded-2xl border border-zinc-800 bg-zinc-900 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-zinc-100">Disk metadata</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Alias</dt><dd class="mt-1 text-zinc-100">{{ $module['alias'] }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Disk version</dt><dd class="mt-1 text-zinc-100">{{ $module['disk_version'] ?: '—' }}</dd></div>
                        <div><dt class="text-xs font-semibold uppercase text-zinc-500">Disk description</dt><dd class="mt-1 text-zinc-100">{{ $module['disk_description'] ?: '—' }}</dd></div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>

<?php

namespace mpba\Modules\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use mpba\Modules\Support\DatabaseModuleRegistry;

class ModuleControlController extends Controller
{
    public function __construct(
        protected DatabaseModuleRegistry $registry,
    ) {}

    public function index(): View
    {
        return view('modules-control::index', [
            'modules' => $this->registry->all(),
        ]);
    }

    public function show(string $module): View
    {
        return view('modules-control::show', [
            'module' => $this->registry->find($module),
        ]);
    }

    public function sync(): RedirectResponse
    {
        $this->registry->syncMissingStatuses();

        return redirect()
            ->route('modules.control.index')
            ->with('status', 'Module database statuses synced.');
    }

    public function enable(string $module): RedirectResponse
    {
        $this->registry->enable($module);

        return redirect()
            ->route('modules.control.index')
            ->with('status', "{$module} enabled in the database.");
    }

    public function disable(string $module): RedirectResponse
    {
        $this->registry->disable($module);

        return redirect()
            ->route('modules.control.index')
            ->with('status', "{$module} disabled in the database.");
    }
}

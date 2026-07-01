<?php

namespace mpba\Modules\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function update(Request $request, string $module): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['nullable', 'string', 'max:2000'],
            'version' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ]);

        $this->registry->update($module, $data);

        return redirect()
            ->route('modules.control.show', $module)
            ->with('status', "{$module} database metadata saved.");
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

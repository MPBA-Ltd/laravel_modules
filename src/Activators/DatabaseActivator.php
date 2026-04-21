<?php

namespace mpba\Modules\Activators;

use Illuminate\Support\Facades\DB;
use mpba\Modules\Contracts\ActivatorInterface;
use mpba\Modules\Module;

class DatabaseActivator implements ActivatorInterface
{
    public function enable(Module $module): void
    {
        $this->setActive($module, true);
    }

    public function disable(Module $module): void
    {
        $this->setActive($module, false);
    }

    public function hasStatus(Module $module, bool $status): bool
    {
        return $this->getStatus($module, ! $status) === $status;
    }

    public function setActive(Module $module, bool $active): void
    {
        $this->setActiveByName($module->getName(), $active);
    }

    public function setActiveByName(string $name, bool $active): void
    {
        DB::table('module_statuses')->updateOrInsert(
            ['module' => $name],
            ['enabled' => $active]
        );
    }

    public function delete(Module $module): void
    {
        DB::table('module_statuses')
            ->where('module', $module->getName())
            ->delete();
    }

    public function reset(): void
    {
        DB::table('module_statuses')->truncate();
    }

    protected function getStatus(Module $module, bool $default = false): bool
    {
        $row = DB::table('module_statuses')
            ->where('module', $module->getName())
            ->first();

        if ($row === null) {
            return $default;
        }

        return (bool) $row->enabled;
    }
}

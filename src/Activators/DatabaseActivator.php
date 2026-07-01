<?php

namespace mpba\Modules\Activators;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use mpba\Modules\Contracts\ActivatorInterface;
use mpba\Modules\Module;

class DatabaseActivator implements ActivatorInterface
{
    /**
     * The table used to store module statuses.
     *
     * @var string
     */
    private const string TABLE_NAME = 'module_statuses';

    /**
     * Enable the given module.
     *
     * @param Module $module
     * @return void
     */
    public function enable(Module $module): void
    {
        $this->setActive($module, true);
    }

    /**
     * Disable the given module.
     *
     * @param Module $module
     * @return void
     */
    public function disable(Module $module): void
    {
        $this->setActive($module, false);
    }

    /**
     * Determine whether the module has the given status.
     *
     * @param Module $module
     * @param bool $status
     * @return bool
     */
    public function hasStatus(Module $module, bool $status): bool
    {
        return $this->getStatus($module, $status) === $status;
    }

    /**
     * Set the active state of the given module.
     *
     * @param Module $module
     * @param bool $active
     * @return void
     */
    public function setActive(Module $module, bool $active): void
    {
        $this->setActiveByName($module->getName(), $active);
    }

    /**
     * Set the active state of the given module by name.
     *
     * @param string $name
     * @param bool $active
     * @return void
     */
    public function setActiveByName(string $name, bool $active): void
    {
        if (! $this->statusesTableExists()) {
            return;
        }

        DB::table(self::TABLE_NAME)->updateOrInsert(
            ['module' => $name],
            ['enabled' => $active]
        );
    }

    /**
     * Delete the stored status for the given module.
     *
     * @param Module $module
     * @return void
     */
    public function delete(Module $module): void
    {
        if (! $this->statusesTableExists()) {
            return;
        }

        DB::table(self::TABLE_NAME)
            ->where('module', $module->getName())
            ->delete();
    }

    /**
     * Reset all module statuses.
     *
     * @return void
     */
    public function reset(): void
    {
        if (! $this->statusesTableExists()) {
            return;
        }

        DB::table(self::TABLE_NAME)->truncate();
    }

    /**
     * Get the stored status for the given module.
     *
     * @param Module $module
     * @param bool $default
     * @return bool
     */
    protected function getStatus(Module $module, bool $default = true): bool
    {
        if (! $this->statusesTableExists()) {
            return $default;
        }

        $row = DB::table(self::TABLE_NAME)
            ->where('module', $module->getName())
            ->first();

        if ($row === null) {
            return $default;
        }

        return (bool) $row->enabled;
    }

    /**
     * Determine whether the statuses table exists.
     *
     * @return bool
     */
    protected function statusesTableExists(): bool
    {
        try {
            return Schema::hasTable(self::TABLE_NAME);
        } catch (\Throwable $exception) {
            return false;
        }
    }
}

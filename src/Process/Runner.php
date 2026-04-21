<?php

namespace mpba\Modules\Process;

use mpba\Modules\Contracts\RepositoryInterface;
use mpba\Modules\Contracts\RunableInterface;

class Runner implements RunableInterface
{

    protected $module;

    public function __construct(RepositoryInterface $module)
    {
        $this->module = $module;
    }

    /**
     * Run the given command.
     *
     * @param  string  $command
     */
    public function run($command)
    {
        passthru($command);
    }
}

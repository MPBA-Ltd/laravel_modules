<?php

namespace mpba\Modules\Commands;

use Illuminate\Support\Str;
use mpba\Modules\Support\Config\GenerateConfigReader;
use mpba\Modules\Support\Stub;
use mpba\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class HelperMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $name = 'module:make-helper';

    protected $description = 'Generate Helpers/helper.php for the specified module.';

    protected $argumentName = 'module';

    public function getDefaultNamespace(): string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.helpers.namespace') ?: $module->config('paths.generator.helpers.path', 'Helpers');
    }

    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, 'The name of module.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['helper', null, InputOption::VALUE_OPTIONAL, 'Unused for now.', null],
        ];
    }

    protected function getTemplateContents()
    {

        return (new Stub($this->getStubName(), [
            'MODULE' => $this->getModuleName(),
            'MODULE_LOWER' => strtolower($this->getModuleName()),
        ]))->render();

    }

    /**
     * @return array|string
     */
    protected function getHelperName()
    {
        $helper = Str::studly($this->option('helper'));

        if (Str::contains(strtolower($helper), 'helper') === false) {
            $helper .= 'Helper';
        }

        return $helper;
    }

    protected function getDestinationFilePath()
    {

        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $helperPath = GenerateConfigReader::read('helper');

        return $path.$helperPath->getPath().'/'.$this->getHelperName().'.php';
    }

    protected function getFileName()
    {
        return 'helper';
    }

    protected function getStubName()
    {
        return '/helper.stub';
    }

    protected function getModuleName(): string
    {
        return $this->argument('module');
    }
}

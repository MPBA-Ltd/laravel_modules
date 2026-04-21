<?php

namespace mpba\Modules\Commands;

use Illuminate\Support\Str;
use mpba\Modules\Support\Config\GenerateConfigReader;
use mpba\Modules\Support\Stub;
use mpba\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SupportMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $name = 'module:make-support';

    protected $description = 'Generate Support files for the specified module.';

    protected $argumentName = 'module';

    public function getDefaultNamespace(): string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.support.namespace') ?: $module->config('paths.generator.support.path', 'Support');
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
            ['support', null, InputOption::VALUE_OPTIONAL, 'Unused for now.', null],
        ];
    }

    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/moduleinfo.stub', [
            'NAMESPACE' => $this->getClassNamespace($module),
            'MODULE' => $this->getModuleName(),
        ]))->render();

    }

    /**
     * @return array|string
     */
    protected function getPathName()
    {
        $pathName = Str::studly($this->option('support'));

        if (Str::contains(strtolower($pathName), 'support') === false) {
            $pathName .= 'moduleInfo';
        }

        return $pathName;
    }

    protected function getDestinationFilePath()
    {

        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $helperPath = GenerateConfigReader::read('support');

        return $path.$helperPath->getPath().'/'.$this->getPathName().'.php';
    }

    protected function getFileName()
    {
        return 'moduleInfo';
    }

    /**
     * Get class namespace.
     *
     * @param  \mpba\Modules\Module  $module
     * @return string
     */
    public function getClassNamespace($module)
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace = $this->laravel['modules']->config('namespace');

        $namespace .= '\\'.$module->getStudlyName();

        $namespace .= '\\'.$this->getDefaultNamespace();

        $namespace .= '\\'.$extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }
}

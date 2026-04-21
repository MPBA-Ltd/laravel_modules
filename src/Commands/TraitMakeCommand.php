<?php

namespace mpba\Modules\Commands;

use Illuminate\Support\Str;
use mpba\Modules\Support\Config\GenerateConfigReader;
use mpba\Modules\Support\Stub;
use mpba\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class TraitMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected $name = 'module:make-trait';

    protected $description = 'Generate a trait for the specified module.';

    protected $argumentName = 'module';

    public function getDefaultNamespace(): string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.trait.namespace') ?: $module->config('paths.generator.trait.path', 'Traits');
    }

    protected function getArguments()
    {
        return [
            ['trait', InputArgument::REQUIRED, 'The name of the trait.'],
            ['module', InputArgument::REQUIRED, 'The name of module.'],
        ];
    }

    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'TRAITNAME' => $this->getTraitName(),
            'NAMESPACE' => $this->getClassNamespace($module),
        ]))->render();

    }

    /**
     * @return array|string
     */
    protected function getTraitName()
    {
        $trait = Str::studly($this->argument('trait'));

        if (Str::contains(strtolower($trait), 'trait') === false) {
            $trait .= 'Trait';
        }

        return $trait;
    }

    protected function getDestinationFilePath()
    {

        $path = $this->laravel['modules']->getModulePath($this->getModuleName());
        $traitPath = GenerateConfigReader::read('trait');

        return $path.$traitPath->getPath().'/'.$this->getTraitName().'.php';
    }

    protected function getFileName()
    {
        return 'trait';
    }

    protected function getStubName()
    {
        return '/trait.stub';
    }

    protected function getModuleName(): string
    {
        return $this->argument('module');
    }
}
